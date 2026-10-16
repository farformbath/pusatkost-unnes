# Milestone 2: Modul Pemesanan & Pembayaran Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development or superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Membangun subsistem pemesanan (*booking*), pembayaran digital/VA, audit log ketersediaan kamar, dan mekanisme pencegahan *double-booking* berbasis transaksi pesimistik (*pessimistic locking*) pada Bounded Context `App\Domain\Booking\`.

**Architecture:** Modular Monolith DDD-lite, Action Pattern, Pessimistic Locking (`lockForUpdate`), Strict Zero Float, UUID Primary Keys, Inertia.js v3, React 19 Strict, Tailwind CSS v4, Pest v3, PHPStan Level Max.

**Spec:** [`docs/superpowers/specs/2026-09-01-milestone-2-booking-design.md`](file:///c:/projects/unneskost2/docs/superpowers/specs/2026-09-01-milestone-2-booking-design.md)

---

## Global Invariants
- `declare(strict_types=1);` pada seluruh berkas PHP baru.
- Nilai uang (`total_amount`, `deposit_amount`, `grand_total`, `gross_amount`) disimpan dalam integer Rupiah `BIGINT`.
- Eksekusi booking dan perubahan ketersediaan kamar wajib dibungkus dalam `DB::transaction()` + `Room::lockForUpdate()`.
- Zero Axios (Inertia native routing & form).
- Strict TypeScript (`"strict": true`, 0 `any`).

---

## Task Breakdown

### Task 1: Database Migrations, Enums, Models & Invariant Exceptions
- [ ] Buat Enums: `BookingStatus.php`, `PaymentStatus.php`.
- [ ] Buat Exception: `RoomUnavailableException.php`.
- [ ] Buat Migrasi:
  - `2026_09_01_000006_create_bookings_table.php` (UUID PK, booking_code, user_id, listing_id, room_id, dates, amounts, status, payment_status).
  - `2026_09_01_000007_create_payments_table.php` (UUID PK, booking_id, invoice_number, payment_channel, gross_amount, transaction_status, payload).
  - `2026_09_01_000008_create_room_status_audits_table.php` (UUID PK, room_id, booking_id, user_id, previous_status, new_status, available_rooms tracking).
- [ ] Buat Eloquent Models: `Booking.php`, `Payment.php`, `RoomStatusAudit.php` (HasUuids, SoftDeletes, casts, relations, computed formatted attributes).
- [ ] Hubungkan relasi pada model `User`, `Listing`, dan `Room`.
- [ ] **Verification:** `php artisan migrate`, Pest unit test relasi & invariants.

### Task 2: Domain Actions & Concurrency Locking (Action Pattern)
- [ ] Buat DTOs: `BookingData.php`, `PaymentData.php`.
- [ ] Buat Domain Actions:
  - `CreateBookingAction.php`: `DB::transaction` + `lockForUpdate` + kalkulasi `grand_total` + decrement `available_rooms` + audit record + invoice creation.
  - `ApproveBookingAction.php`: Owner approval $\rightarrow$ status `confirmed`.
  - `RejectBookingAction.php`: Owner rejection $\rightarrow$ status `rejected` + restore `available_rooms` + audit log.
  - `CancelBookingAction.php`: User cancellation $\rightarrow$ status `cancelled` + restore `available_rooms` + audit log.
  - `ProcessPaymentAction.php`: Status update `paid` / `settlement` $\rightarrow$ booking status `active`.
- [ ] Buat Repository: `BookingRepositoryInterface.php` & `EloquentBookingRepository.php`.
- [ ] Daftarkan repository binding di `AppServiceProvider.php`.
- [ ] **Verification:** Pest Unit Tests (`CreateBookingActionTest`, `ConcurrencyLockTest`, `CancelBookingTest`).

### Task 3: HTTP Layer (Controllers, FormRequests, Policies & Routes)
- [ ] Buat FormRequests: `StoreBookingRequest.php`, `SimulatePaymentRequest.php`.
- [ ] Buat Policies: `BookingPolicy.php`.
- [ ] Buat Controllers:
  - `Booking/BookingController.php` (`create/checkout`, `store`, `show`, `cancel`).
  - `Booking/PaymentController.php` (`simulatePay`).
  - `Owner/BookingManagementController.php` (`index`, `approve`, `reject`).
- [ ] Daftarkan routes di `routes/web.php`.
- [ ] **Verification:** Feature tests HTTP endpoints (`BookingCheckoutTest`, `OwnerBookingApprovalTest`).

### Task 4: Frontend Components & Pages (React 19 + Inertia v3 + Taste Skill)
- [ ] Buat TypeScript types di `resources/js/types/booking.ts`.
- [ ] Update `resources/js/Pages/Listings/Show.tsx` dengan form pemesanan interaktif (Pilih kamar $\rightarrow$ Redirect ke Checkout).
- [ ] Buat Halaman Booking:
  - `resources/js/Pages/Booking/Checkout.tsx`: Review detail kos, pilihan kamar, tanggal masuk, durasi, dan rincian harga.
  - `resources/js/Pages/Booking/Show.tsx`: Halaman instruksi pembayaran (Virtual Account / QRIS simulasi, status badge, tombol bayar instan).
  - `resources/js/Pages/Booking/Success.tsx`: Layar konfirmasi pemesanan sukses.
- [ ] Buat Halaman Owner:
  - `resources/js/Pages/Owner/Bookings/Index.tsx`: Manajemen persetujuan booking masuk pemilik kos (Tabel booking, tombol Setujui & Tolak).
- [ ] **Verification:** `npx tsc --noEmit` (0 error), `npm run lint`, `npm run build`.

### Task 5: Full Verification Gate & Milestone 2 Completion
- [ ] `composer validate`
- [ ] `./vendor/bin/pint --test`
- [ ] `./vendor/bin/phpstan analyse` (Level Max)
- [ ] `php artisan test` (Full Pest suite hijau)
- [ ] `npx tsc --noEmit`
- [ ] `npm run lint`
- [ ] `npm run build`
- [ ] Update `tasks.md` & walkthrough.
