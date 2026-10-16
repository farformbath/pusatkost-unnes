# 🚀 Walkthrough: Milestone 2 — Modul Pemesanan & Pembayaran (Anti Double-Booking) Selesai

- **Milestone:** Milestone 2 (M2)
- **Bounded Context:** `App\Domain\Booking\`
- **Status:** `COMPLETED` 🟢
- **Tanggal Selesai:** 2026-09-01

---

## 📦 1. Apa yang Telah Diimplementasikan

### A. Database & Invariant PostgreSQL 16
- **Tabel `bookings`**:
  - UUID Primary Key (`id`), unique `booking_code`, FK relasi ke `users`, `listings`, dan `rooms`.
  - Field tanggal: `start_date`, `duration_months` (1-12), `end_date`, `expired_at` (24 jam window pembayaran).
  - **Strict Zero Float**: `total_amount`, `deposit_amount`, `grand_total` disimpan dalam integer Rupiah `BIGINT`.
  - Status enums terindeks: `status` (`pending`, `approved`, `rejected`, `cancelled`, `expired`, `completed`) dan `payment_status` (`pending`, `paid`, `failed`, `expired`, `cancelled`).
- **Tabel `payments`**:
  - UUID PK, `invoice_number` unik, `payment_channel`, `payment_reference` (Virtual Account / QR Code), `gross_amount` (`BIGINT`), `transaction_status`, `payload` (`JSONB`), `paid_at`.
- **Tabel `room_status_audits`**:
  - Audit trail ketersediaan kamar: `previous_status`, `new_status`, `previous_available_count`, `new_available_count`, dan `reason`.

### B. Anti Double-Booking Pessimistic Locking Strategy
```php
DB::transaction(function () use ($data): Booking {
    /** @var Room $room */
    $room = Room::query()
        ->where('id', $data->roomId)
        ->lockForUpdate()
        ->firstOrFail();

    // 1. Cek availability di dalam lock
    if ($room->status !== RoomStatus::AVAILABLE || $room->available_rooms <= 0) {
        throw new RoomUnavailableException('Kamar yang Anda pilih baru saja terisi penuh atau tidak tersedia.');
    }

    // 2. Kalkulasi biaya bulat integer (Zero Float)
    $totalAmount = (int) $room->price_monthly * $data->durationMonths;
    $depositAmount = (int) $room->price_deposit;
    $grandTotal = $totalAmount + $depositAmount;

    // 3. Kurangi ketersediaan kamar secara atomik
    $prevAvailable = $room->available_rooms;
    $newAvailable = $prevAvailable - 1;
    $newStatus = $newAvailable === 0 ? RoomStatus::OCCUPIED : RoomStatus::AVAILABLE;

    $room->update(['available_rooms' => $newAvailable, 'status' => $newStatus]);

    // 4. Catat booking, invoice pembayaran, dan audit trail dalam 1 transaksi
    ...
});
```

### C. Booking & Payment State Machine
```
[PENGAJUAN SEWA]
       │
       ▼
   [PENDING] ──(Batas Waktu 24 Jam Habis)──> [EXPIRED] (Stok Kembali)
       │
       ├────(Pemilik Menolak)──────────────> [REJECTED] (Stok Kembali)
       │
       ├────(Penyewa Membatalkan)──────────> [CANCELLED] (Stok Kembali)
       │
       ├────(Pemilik Menyetujui)───────────> [APPROVED]
       │                                         │
       ▼                                         ▼
   [BAYAR (Sandbox / VA)] ─────────────────> [PAID / ACTIVE] ──(Selesai Masa Sewa)──> [COMPLETED]
```

### D. Action Pattern & Repositories
- `CreateBookingAction`: Transaction + Lock pesimistik + validasi ketersediaan + kalkulasi harga integer + decrement stok kamar + audit log.
- `ApproveBookingAction`: State transition guard (`pending` $\rightarrow$ `approved`).
- `RejectBookingAction`: Rejection + restore ketersediaan kamar secara atomik + audit log.
- `CancelBookingAction`: Idempotent cancellation + restore ketersediaan kamar secara atomik + audit log.
- `ProcessPaymentAction`: Idempotent settlement payment + update booking payment status.
- `ExpireBookingsAction` & `ExpireBookingsCommand` (`php artisan app:expire-bookings`): Otomatisasi pembatalan dan pengembalian kamar yang melewati batas 24 jam.
- `BookingRepositoryInterface` & `EloquentBookingRepository`: Query concern terisolasi dengan eager loading optimal.

### E. Frontend React 19 (Taste Skill & Anti-Slop)
- `resources/js/types/booking.ts`: TypeScript strict interfaces.
- `resources/js/Pages/Booking/Checkout.tsx`: Form pemesanan mobile-first, datepicker, selector durasi, kalkulasi harga real-time integer Rupiah, rincian biaya transparan.
- `resources/js/Pages/Booking/Show.tsx`: Halaman tagihan invoice, countdown timer 24 jam, BCA Virtual Account sandbox simulator, tombol bayar instan, dan pembatalan pesanan.
- `resources/js/Pages/Booking/Success.tsx`: Layar konfirmasi pengajuan sewa sukses.
- `resources/js/Pages/Owner/Bookings/Index.tsx`: Dashboard persetujuan pemilik kos dengan filter status tab dan modal konfirmasi penolakan/persetujuan.

---

## 🧪 2. Bukti Hasil Verifikasi Mandiri (Verification Gate)

```
┌──────────────────────────────────────┬─────────────────────────────┐
│ Checkpoint Pengujian                 │ Status & Hasil              │
├──────────────────────────────────────┼─────────────────────────────┤
│ Composer Validate                    │ Valid (Exit 0)              │
│ Laravel Pint (Code Formatting)       │ Passed (Exit 0)             │
│ PHPStan Level Max (Static Analysis)  │ 83/83 [OK] No errors        │
│ Pest Testing Suite (Unit & Feature)  │ 35 passed (130 assertions)  │
│ ConcurrencyLockTest (Anti-Double)    │ Passed (Zero Double Booking)│
│ TypeScript Type Check (tsc --noEmit) │ 0 error (Strict Mode)       │
│ ESLint 9 Flat Config                 │ Passed (0 error/warning)    │
│ Vite Production Asset Build          │ Built in 4.44s              │
│ Graphify Knowledge Graph             │ 625 nodes, 1329 edges       │
└──────────────────────────────────────┴─────────────────────────────┘
```

---

## 📁 3. Daftar Berkas Baru & Modifikasi

1. `app/Domain/Booking/Enums/BookingStatus.php`
2. `app/Domain/Booking/Enums/PaymentStatus.php`
3. `app/Domain/Booking/Exceptions/RoomUnavailableException.php`
4. `app/Domain/Booking/Models/Booking.php`
5. `app/Domain/Booking/Models/Payment.php`
6. `app/Domain/Booking/Models/RoomStatusAudit.php`
7. `app/Domain/Booking/DTOs/BookingData.php`
8. `app/Domain/Booking/DTOs/PaymentData.php`
9. `app/Domain/Booking/Actions/CreateBookingAction.php`
10. `app/Domain/Booking/Actions/ApproveBookingAction.php`
11. `app/Domain/Booking/Actions/RejectBookingAction.php`
12. `app/Domain/Booking/Actions/CancelBookingAction.php`
13. `app/Domain/Booking/Actions/ProcessPaymentAction.php`
14. `app/Domain/Booking/Actions/ExpireBookingsAction.php`
15. `app/Console/Commands/ExpireBookingsCommand.php`
16. `app/Domain/Booking/Repositories/BookingRepositoryInterface.php`
17. `app/Domain/Booking/Repositories/EloquentBookingRepository.php`
18. `app/Domain/Booking/Policies/BookingPolicy.php`
19. `app/Http/Requests/Booking/StoreBookingRequest.php`
20. `app/Http/Requests/Booking/SimulatePaymentRequest.php`
21. `app/Http/Controllers/Booking/BookingController.php`
22. `app/Http/Controllers/Booking/PaymentController.php`
23. `app/Http/Controllers/Owner/BookingManagementController.php`
24. `database/migrations/2026_09_01_000006_create_bookings_table.php`
25. `database/migrations/2026_09_01_000007_create_payments_table.php`
26. `database/migrations/2026_09_01_000008_create_room_status_audits_table.php`
27. `resources/js/types/booking.ts`
28. `resources/js/Pages/Booking/Checkout.tsx`
29. `resources/js/Pages/Booking/Show.tsx`
30. `resources/js/Pages/Booking/Success.tsx`
31. `resources/js/Pages/Owner/Bookings/Index.tsx`
32. `tests/Unit/Domain/Booking/BookingModelTest.php`
33. `tests/Unit/Domain/Booking/CreateBookingActionTest.php`
34. `tests/Unit/Domain/Booking/ConcurrencyLockTest.php`
35. `tests/Feature/BookingFlowTest.php`
36. `tests/Feature/OwnerBookingApprovalTest.php`
37. Modifikasi: `app/Domain/Listing/Models/Room.php`, `Listing.php`, `app/Models/User.php`, `app/Providers/AppServiceProvider.php`, `routes/web.php`, `resources/js/Pages/Listings/Show.tsx`.
