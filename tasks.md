# 📋 Master Task Tracker — UNNESKost v3

Dokumen ini adalah pusat pelacakan status implementasi, pembagian tugas (*task decomposition*), dan verifikasi *Definition of Done* (DoD) per milestone untuk proyek **UNNESKost v3**.

Acuan utama spesifikasi: [`requirements.md`](requirements.md) dan [`unneskost-prompt-final.md`](unneskost-prompt-final.md).

---

## 🗺️ Roadmap Milestone Overview

```
[M0: Fondasi] (DONE) ──> [M1: Listing & Kamar] ──> [M2: Booking & Payment]
                                                              │
[M6: Polish & E2E] <── [M5: Auth & RBAC] <── [M4: Review & Fav] <── [M3: Search & Filter]
```

---

## ✅ Milestone 0: Fondasi & Scaffolding Codebase
**Status:** `COMPLETED` 🟢 (Verified 2026-08-30)

- [x] **Task 0.1: Scaffolding Framework & Verified Dependencies**
  - [x] Laravel 12/13 base scaffolding (`composer.json`, `routes/`, `bootstrap/app.php`).
  - [x] Backend dependencies: `inertiajs/inertia-laravel:^3.0`, `spatie/laravel-permission:^6.0`, `spatie/typescript-transformer:^3.0`, `laravel/sanctum:^4.0`, `pestphp/pest:^3.0`, `larastan/larastan:^3.0`, `laravel/pint:^1.0`.
  - [x] Frontend dependencies: `@inertiajs/react:^3.0.0`, `react@^19.0.0`, `tailwindcss@^4.0.0`, `typescript:^5.0.0`, `clsx`, `tailwind-merge`, `eslint`.
  - [x] **Zero Axios rule verified.**
- [x] **Task 0.2: Core Application & Strict Foundations**
  - [x] `AppServiceProvider`: `declare(strict_types=1);` dan `Model::shouldBeStrict(! app()->isProduction());`.
  - [x] `App\Support\Money`: Invariant integer-only Rupiah, penolakan floating point/negatif, helper `format()`.
  - [x] Autoload root namespace `"App\\Domain\\": "app/Domain/"`.
  - [x] Pest unit test `tests/Unit/Support/MoneyTest.php` (4/4 passed).
- [x] **Task 0.3: Docker Infrastructure & Local Dev Tooling**
  - [x] `docker-compose.yml`: 8 service (`app`, `nginx`, `postgres 16`, `redis 7`, `meilisearch`, `mailpit`, `queue-worker`, `scheduler`).
  - [x] `docker/php/Dockerfile`: PHP 8.4 Alpine non-root container (`pdo_pgsql`, `redis`, `intl`, `bcmath`, `zip`, `opcache`).
  - [x] `docker/nginx/default.conf`: PHP-FPM integration + HTTP Security Headers.
  - [x] `Makefile` & `.env.example`.
- [x] **Task 0.4: Code Quality & CI Automation**
  - [x] `phpstan.neon`: Level Max (Level 9) + Larastan integration.
  - [x] `pint.json`: Preset Laravel + `declare_strict_types`.
  - [x] `tsconfig.json`: `"strict": true`, `"noImplicitAny": true`, `"types": ["vite/client"]`.
  - [x] `eslint.config.js`: ESLint flat config strict mode.
  - [x] `.github/workflows/ci.yml` & `.github/workflows/security.yml`.
- [x] **Task 0.5: Inertia React Entrypoint & Brand Identity**
  - [x] `resources/views/app.blade.php`, `resources/css/app.css` (Tailwind v4 theme variables).
  - [x] `resources/js/components/shared/Logo.tsx` (horizontal & square variants).
  - [x] `resources/js/Pages/Welcome.tsx` (Verification page).
  - [x] Route `GET /` me-render Inertia Welcome page.
- [x] **Task 0.6: Full Verification Gate**
  - [x] `composer validate` -> OK
  - [x] `./vendor/bin/pint --test` -> Passed
  - [x] `./vendor/bin/phpstan analyse` -> [OK] No errors
  - [x] `php artisan test` -> 6 passed (11 assertions)
  - [x] `npx tsc --noEmit` -> 0 errors
  - [x] `npm run lint` -> Passed
  - [x] `npm run build` -> Passed (4.27s)
  - [x] Docker runtime: 8 services healthy & database migrated.

---

## 🔲 Milestone 1: Modul Listing & Manajemen Kamar
**Status:** `READY TO START` ⏳  
**Bounded Context:** `App\Domain\Listing\`

- [ ] **Task 1.1: Database Schema & Invariants (PostgreSQL 16)**
  - [ ] Migration `create_listings_table` (UUID PK, owner_id FK, name, slug unique, gender_type enum, address, coordinates postgis/lat-long, status enum, description, rules, facilities jsonb/relation).
  - [ ] Migration `create_rooms_table` (UUID PK, listing_id FK, room_number, size, price_monthly BIGINT, price_deposit BIGINT, status enum, total_rooms, available_rooms).
  - [ ] Migration `create_facilities_table` & pivot `facility_listing` / `facility_room`.
  - [ ] Migration `create_listing_images_table` (UUID PK, listing_id, room_id nullable, url, is_primary, order).
  - [ ] Model invariants & Eloquent relations (`Listing`, `Room`, `Facility`, `ListingImage`).
  - [ ] Enums: `GenderType`, `ListingStatus`, `RoomStatus`.
- [ ] **Task 1.2: Domain Actions & Services (Action Pattern)**
  - [ ] `CreateListingAction`, `UpdateListingAction`, `PublishListingAction`.
  - [ ] `AddRoomToListingAction`, `UpdateRoomAction`, `ToggleRoomAvailabilityAction`.
  - [ ] `UploadListingImagesAction`, `ReorderImagesAction`, `DeleteImageAction`.
  - [ ] Repository: `ListingRepositoryInterface` & `EloquentListingRepository`.
- [ ] **Task 1.3: HTTP Layer (Thin Controllers & Requests)**
  - [ ] FormRequests: `StoreListingRequest`, `UpdateListingRequest`, `StoreRoomRequest`, `UploadImageRequest`.
  - [ ] Controllers: `Owner\ListingController`, `Owner\RoomController`, `Public\ListingController`.
  - [ ] API Resources / Inertia DTO props.
- [ ] **Task 1.4: Frontend Pages & Components (React 19 + Inertia v3 + Tailwind v4)**
  - [ ] `resources/js/components/ui/` primitives (Button, Modal, Input, Badge, Select, Card, Tabs).
  - [ ] `resources/js/features/listings/components/ListingCard.tsx`.
  - [ ] `resources/js/features/listings/components/RoomCard.tsx`.
  - [ ] `resources/js/features/listings/components/ImageGallery.tsx`.
  - [ ] `resources/js/features/listings/components/FacilityBadge.tsx`.
  - [ ] Public Pages: `resources/js/Pages/Listings/Index.tsx`, `resources/js/Pages/Listings/Show.tsx`.
  - [ ] Owner Pages: `resources/js/Pages/Owner/Listings/Create.tsx`, `resources/js/Pages/Owner/Listings/Edit.tsx`, `resources/js/Pages/Owner/Rooms/Index.tsx`.
- [ ] **Task 1.5: Testing & Verification**
  - [ ] Unit Tests: `ListingTest`, `RoomTest`, `CreateListingActionTest`.
  - [ ] Feature Tests: `PublicListingTest`, `OwnerListingCrudTest`, `ImageUploadTest`.
  - [ ] Quality Gate: `phpstan` max, `pint`, `tsc --noEmit`, `npm run lint`, `npm run build`.

---

## 🔲 Milestone 2: Modul Pemesanan & Pembayaran
**Status:** `PENDING` ⏸️  
**Bounded Context:** `App\Domain\Booking\`

- [ ] **Task 2.1: Database Schema & Anti-Double-Booking Locking**
  - [ ] Migration `create_bookings_table` (UUID PK, booking_code unique, user_id, room_id, listing_id, check_in_date, duration_months, end_date, total_amount BIGINT, deposit_amount BIGINT, status enum, payment_status enum, created_at, expired_at).
  - [ ] Migration `create_payments_table` (UUID PK, booking_id, invoice_number, payment_channel, gross_amount BIGINT, transaction_status, snap_token/payload, paid_at).
  - [ ] Migration `create_room_status_audits_table` (audit log perubahan ketersediaan kamar).
  - [ ] Enums: `BookingStatus` (`pending`, `confirmed`, `active`, `completed`, `cancelled`, `rejected`), `PaymentStatus`.
- [ ] **Task 2.2: Booking Flow & Concurrency Lock (Action Pattern)**
  - [ ] `CreateBookingAction`: Validasi availability dengan `DB::transaction()` + `Room::lockForUpdate()`.
  - [ ] `CancelBookingAction`, `ApproveBookingAction`, `RejectBookingAction`.
  - [ ] Domain Events: `BookingCreated`, `BookingConfirmed`, `BookingCancelled`, `PaymentSuccessful`.
  - [ ] Payment Gateway Integration (Midtrans / Xendit Sandbox client).
  - [ ] `HandlePaymentWebhookAction`: Idempotency key check, update status pembayaran, transisi status kamar.
- [ ] **Task 2.3: Frontend Booking & Payment UI**
  - [ ] `resources/js/features/booking/components/BookingWidget.tsx` (Sticky di desktop, bottom sheet di mobile).
  - [ ] `resources/js/features/booking/components/BookingSummary.tsx`.
  - [ ] `resources/js/Pages/Booking/Checkout.tsx`, `resources/js/Pages/Booking/Success.tsx`, `resources/js/Pages/Booking/Show.tsx`.
  - [ ] `resources/js/Pages/Owner/Bookings/Index.tsx` (Manajemen persetujuan booking owner).
- [ ] **Task 2.4: Testing & Concurrency Verification**
  - [ ] Concurrency Test: 2 user memesan 1 kamar tersisa secara simultan (Pest parallel / lock test).
  - [ ] Webhook Idempotency Test.
  - [ ] Quality Gate: `phpstan` max, `pint`, `tsc --noEmit`.

---

## 🔲 Milestone 3: Modul Pencarian & Filter Cerdas
**Status:** `PENDING` ⏸️  
**Bounded Context:** `App\Domain\Search\`

- [ ] **Task 3.1: Laravel Scout & Meilisearch Indexing**
  - [ ] Scout searchable configuration on `Listing` model (`toSearchableArray()`).
  - [ ] Filterable attributes: `gender_type`, `price_monthly`, `status`, `campus_distance`, `facilities`.
  - [ ] Sortable attributes: `price_monthly`, `created_at`, `rating_avg`.
  - [ ] Queued listener for real-time index synchronization.
  - [ ] Database fallback query saat Meilisearch unreachable/offline.
- [ ] **Task 3.2: Search & Filter HTTP Query Object**
  - [ ] `ListingSearchQuery` (DTO filter parameter: gender, min_price, max_price, facilities[], sort, radius).
  - [ ] API Resource `/api/v1/search/listings`.
- [ ] **Task 3.3: Frontend Search Experience**
  - [ ] `resources/js/features/listings/components/FilterBar.tsx` (Mobile filter drawer + desktop sticky sidebar).
  - [ ] Debounced instant search without page reload (Inertia partial reload).
  - [ ] Map view toggle / radius selector sekitar UNNES (Sekaran, Banaran, Taman Siswa).
  - [ ] Empty state & no-results recommendations.
- [ ] **Task 3.4: Testing & Verification**
  - [ ] Scout Meilisearch sync tests.
  - [ ] Database fallback query tests.

---

## 🔲 Milestone 4: Modul Review, Rating & Wishlist
**Status:** `PENDING` ⏸️  
**Bounded Contexts:** `App\Domain\Review\`, `App\Domain\Favorite\`

- [ ] **Task 4.1: Review & Rating Invariants**
  - [ ] Migration `create_reviews_table` (UUID PK, user_id, listing_id, booking_id unique/verified, rating 1-5, cleanliness_rating, facility_rating, location_rating, comment, photos jsonb, status enum: pending/approved/rejected).
  - [ ] Invariant: Hanya user dengan booking `completed` yang berhak memberi review.
  - [ ] `SubmitReviewAction`, `ModerateReviewAction` (Admin).
  - [ ] Event `ReviewApproved` $\rightarrow$ recalculate `rating_avg` dan `review_count` pada Listing.
- [ ] **Task 4.2: Wishlist / Favorites**
  - [ ] Migration `create_favorites_table` (user_id + listing_id composite unique).
  - [ ] `ToggleFavoriteAction`.
- [ ] **Task 4.3: Frontend UI**
  - [ ] `resources/js/features/review/components/ReviewList.tsx` & `ReviewForm.tsx`.
  - [ ] `resources/js/features/favorites/components/FavoriteButton.tsx` (Optimistic UI update).
  - [ ] `resources/js/Pages/Favorites/Index.tsx`.
  - [ ] `resources/js/Pages/Admin/Reviews/Index.tsx` (Moderasi review admin).
- [ ] **Task 4.4: Testing & Verification**
  - [ ] Review authorization tests (unverified booking cannot review).
  - [ ] Rating average recalculation tests.

---

## 🔲 Milestone 5: Autentikasi, RBAC & Notifikasi
**Status:** `PENDING` ⏸️  
**Bounded Context:** `App\Domain\User\`

- [ ] **Task 5.1: RBAC & Permissions (Spatie)**
  - [ ] Seed roles: `admin`, `owner`, `user`.
  - [ ] Seed permissions: `manage-listings`, `manage-rooms`, `approve-bookings`, `moderate-reviews`, `manage-users`.
  - [ ] Laravel Policies: `ListingPolicy`, `RoomPolicy`, `BookingPolicy`, `ReviewPolicy`.
  - [ ] Dilarang hardcode role check `if ($user->role === 'admin')` di controller.
- [ ] **Task 5.2: Authentication Flow**
  - [ ] Auth pages: `Login.tsx`, `Register.tsx` (pilihan mendaftar sebagai Pencari Kos atau Pemilik Kos), `ForgotPassword.tsx`, `ResetPassword.tsx`.
  - [ ] Email verification & password reset mail (Mailpit in local dev).
  - [ ] Sanctum token generation for `/api/v1`.
- [ ] **Task 5.3: In-App & Email Notifications**
  - [ ] Notification channels: Database (in-app bell) & Mail (queued).
  - [ ] Notifications: `BookingReceivedNotification`, `BookingStatusUpdatedNotification`, `PaymentConfirmedNotification`.
  - [ ] Notification drawer / dropdown component.
- [ ] **Task 5.4: User Profile & Security Settings**
  - [ ] `resources/js/Pages/Profile/Edit.tsx` (ganti password, nomor WhatsApp aktif, foto profil).
- [ ] **Task 5.5: Testing & Verification**
  - [ ] Policy & Gate unit tests.
  - [ ] Auth feature tests (registration, login, role redirection).

---

## 🔲 Milestone 6: Polishing, Observability, Error Boundaries & E2E
**Status:** `PENDING` ⏸️

- [ ] **Task 6.1: Observability & Health Checks**
  - [ ] Endpoint `/health` (cek DB connection, Redis ping, Meilisearch health, disk space, mailer).
  - [ ] Correlation ID Middleware (X-Correlation-ID header pada setiap request & log).
  - [ ] JSON Structured Logging.
- [ ] **Task 6.2: Frontend UX Polish & Error Handling**
  - [ ] React Error Boundaries pada setiap layout.
  - [ ] Custom 404, 403, 500 Inertia Error pages.
  - [ ] Skeleton loaders & empty state illustrations.
  - [ ] Toast notification system (`useToast`).
- [ ] **Task 6.3: E2E Playwright Testing**
  - [ ] Setup Playwright.
  - [ ] Test flow: Register $\rightarrow$ Search $\rightarrow$ View Detail $\rightarrow$ Book Room $\rightarrow$ Owner Approve $\rightarrow$ Simulated Payment.
- [ ] **Task 6.4: Final Production Readiness**
  - [ ] Security headers check (CSP, HSTS, X-Frame-Options).
  - [ ] Final `phpstan analyse` max, `pint`, `tsc --noEmit`, `npm run lint`, `npm run build`.
  - [ ] Dokumentasi akhir & release notes.

---

## 📌 Aturan Pembaruan Dokumen
1. Setiap kali task selesai dikerjakan dan diverifikasi dengan test riil, ubah status `[ ]` menjadi `[x]`.
2. Dilarang menandai task selesai jika verifikasi perintah belum dijalankan.
3. Catat tanggal penyelesaian pada setiap milestone yang selesai.
