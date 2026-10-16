# 📐 Design Specification: Milestone 2 — Modul Pemesanan, Pembayaran & Anti Double-Booking

- **Tanggal:** 2026-09-01
- **Bounded Context:** `App\Domain\Booking\`
- **Status:** In Progress
- **Referensi Kebutuhan:** [`requirements.md`](file:///c:/projects/unneskost2/requirements.md) (FR-BOOK-001 s.d. FR-BOOK-008, FR-PAY-001 s.d. FR-PAY-006).

---

## 1. Arsitektur Domain & Boundary

Bounded Context `App\Domain\Booking\` mengelola seluruh siklus pemesanan kamar kos, audit ketersediaan, dan pembayaran.

```
app/Domain/Booking/
├── Actions/
│   ├── CreateBookingAction.php       # Transaction + Room::lockForUpdate() + Concurrency Guard
│   ├── ApproveBookingAction.php      # Owner approval
│   ├── RejectBookingAction.php       # Owner rejection -> restore room available count
│   ├── CancelBookingAction.php       # User cancellation -> restore room available count
│   └── ProcessPaymentWebhookAction.php # Idempotency check + status transition
├── DTOs/
│   ├── BookingData.php
│   └── PaymentData.php
├── Enums/
│   ├── BookingStatus.php             # PENDING, CONFIRMED, ACTIVE, COMPLETED, CANCELLED, REJECTED
│   └── PaymentStatus.php             # UNPAID, PENDING, PAID, FAILED, EXPIRED, REFUNDED
├── Events/
│   ├── BookingCreated.php
│   ├── BookingApproved.php
│   ├── BookingCancelled.php
│   └── PaymentCompleted.php
├── Exceptions/
│   └── RoomUnavailableException.php  # Thrown when room is fully booked during concurrent lock
├── Models/
│   ├── Booking.php                   # UUID PK, code, dates, BIGINT total & deposit, relations
│   ├── Payment.php                   # UUID PK, invoice, channels, gross_amount, status
│   └── RoomStatusAudit.php           # Audit log perubahan ketersediaan kamar
└── Repositories/
    ├── BookingRepositoryInterface.php
    └── EloquentBookingRepository.php
```

---

## 2. Skema Database PostgreSQL 16

### A. Tabel `bookings`
- `id` (UUID, PK)
- `booking_code` (VARCHAR 50, Unique, e.g. `BK-20260901-8X9A`)
- `user_id` (BIGINT FK -> `users.id`, Cascading, Indexed)
- `listing_id` (UUID FK -> `listings.id`, Cascading, Indexed)
- `room_id` (UUID FK -> `rooms.id`, Cascading, Indexed)
- `check_in_date` (DATE)
- `duration_months` (INTEGER, 1 - 12)
- `end_date` (DATE)
- `total_amount` (BIGINT - Invariant: Integer Rupiah, `price_monthly * duration_months`)
- `deposit_amount` (BIGINT - Invariant: Integer Rupiah)
- `grand_total` (BIGINT - Invariant: `total_amount + deposit_amount`)
- `status` (VARCHAR 20 - `pending`, `confirmed`, `active`, `completed`, `cancelled`, `rejected`, Indexed)
- `payment_status` (VARCHAR 20 - `unpaid`, `pending`, `paid`, `failed`, `expired`, Indexed)
- `notes` (TEXT, Nullable)
- `expired_at` (TIMESTAMP - Batas waktu pembayaran e.g. 24 jam)
- `created_at`, `updated_at`, `deleted_at`

### B. Tabel `payments`
- `id` (UUID, PK)
- `booking_id` (UUID FK -> `bookings.id`, Cascading, Indexed)
- `invoice_number` (VARCHAR 50, Unique, e.g. `INV-20260901-7712`)
- `payment_channel` (VARCHAR 50 - `bca_va`, `bni_va`, `mandiri_va`, `qris`, `manual_transfer`)
- `gross_amount` (BIGINT - Invariant: Integer Rupiah)
- `transaction_status` (VARCHAR 30 - `pending`, `settlement`, `expire`, `cancel`)
- `payment_reference` (VARCHAR 100, Nullable - Nomor VA / Kode Bayar)
- `payload` (JSONB, Nullable)
- `paid_at` (TIMESTAMP, Nullable)
- `created_at`, `updated_at`

### C. Tabel `room_status_audits`
- `id` (UUID, PK)
- `room_id` (UUID FK -> `rooms.id`, Cascading, Indexed)
- `booking_id` (UUID FK -> `bookings.id`, Nullable, Cascading)
- `user_id` (BIGINT FK -> `users.id`, Nullable)
- `previous_status` (VARCHAR 20)
- `new_status` (VARCHAR 20)
- `previous_available_rooms` (INTEGER)
- `new_available_rooms` (INTEGER)
- `reason` (VARCHAR 255)
- `created_at` (TIMESTAMP)

---

## 3. Anti-Double-Booking Concurrency Algorithm

```php
DB::transaction(function () use ($data) {
    // 1. Lock record kamar secara pesimistik
    $room = Room::where('id', $data->roomId)->lockForUpdate()->firstOrFail();

    // 2. Cek ketersediaan aktual
    if ($room->status !== RoomStatus::AVAILABLE || $room->available_rooms <= 0) {
        throw new RoomUnavailableException('Kamar ini baru saja terisi penuh oleh pemesan lain.');
    }

    // 3. Kurangi stok kamar secara atomik
    $prevAvailable = $room->available_rooms;
    $newAvailable = $prevAvailable - 1;
    $newStatus = $newAvailable === 0 ? RoomStatus::OCCUPIED : RoomStatus::AVAILABLE;

    $room->update([
        'available_rooms' => $newAvailable,
        'status' => $newStatus,
    ]);

    // 4. Catat audit status ketersediaan
    RoomStatusAudit::create([
        'room_id' => $room->id,
        'previous_status' => $room->status->value,
        'new_status' => $newStatus->value,
        'previous_available_rooms' => $prevAvailable,
        'new_available_rooms' => $newAvailable,
        'reason' => 'Booking dibuat: ' . $bookingCode,
    ]);

    // 5. Buat entitas booking & invoice pembayaran
    ...
});
```

---

## 4. Antarmuka Pengguna (Frontend React 19 + Taste Skill)

1. **`BookingWidget.tsx`**: Widget pemesanan pada halaman detail kos (pilih tanggal masuk, durasi sewa, kalkulasi harga real-time).
2. **`Pages/Booking/Checkout.tsx`**: Formulir konfirmasi data penyewa (nama, nomor WA, catatan) dan rincian tagihan.
3. **`Pages/Booking/Show.tsx`**: Halaman rincian pesanan & tagihan (Nomor VA, QRIS simulation, countdown timer, dan tombol simulasi bayar).
4. **`Pages/Owner/Bookings/Index.tsx`**: Dashboard persetujuan booking pemilik kos (Setujui / Tolak booking masuk).
