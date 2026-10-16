# 📋 Master Task Tracker — UNNESKost v3

Dokumen ini adalah pusat pelacakan status implementasi, pembagian tugas (*task decomposition*), dan verifikasi *Definition of Done* (DoD) per milestone untuk proyek **UNNESKost v3**.

Acuan utama spesifikasi: [`requirements.md`](requirements.md) dan [`unneskost-prompt-final.md`](unneskost-prompt-final.md).

---

## 🗺️ Roadmap Milestone Overview

```
[M0: Fondasi] (DONE) ──> [M1: Listing & Kamar] ──> [M2: Booking & Payment]
                                                              │
[M7: NESA AI Agentic RAG] <── [M6: Polish & E2E] <── [M5: Auth & RBAC] <── [M4: Review & Fav] <── [M3: Search & Filter]
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

## ✅ Milestone 1: Modul Listing & Manajemen Kamar
**Status:** `COMPLETED` 🟢 (Verified 2026-09-01)  
**Bounded Context:** `App\Domain\Listing\`

- [x] **Task 1.1: Database Schema & Invariants (PostgreSQL 16)**
  - [x] Migration `create_facilities_table` (UUID PK, name, slug, icon, category).
  - [x] Migration `create_listings_table` (UUID PK, owner_id FK, name, slug unique, gender_type enum, address, area, lat/long, rules, status enum, rating_avg, review_count).
  - [x] Migration `create_rooms_table` (UUID PK, listing_id FK, name, size, price_monthly BIGINT, price_deposit BIGINT, status enum, total_rooms, available_rooms).
  - [x] Migration `create_listing_images_table` (UUID PK, listing_id FK, room_id FK nullable, file_path, is_primary, sort_order).
  - [x] Migration `create_facility_pivots_table` (`facility_listing` dan `facility_room`).
  - [x] Eloquent models: `Facility`, `Listing`, `Room`, `ListingImage` (HasUuids, SoftDeletes, Enums, relations, scopes, price formatting).
  - [x] Enums: `GenderType`, `ListingStatus`, `RoomStatus`.
  - [x] Seeder: `FacilitySeeder` & `DatabaseSeeder`.
- [x] **Task 1.2: Domain Actions & Services (Action Pattern)**
  - [x] `CreateListingAction`, `UpdateListingAction`, `PublishListingAction`.
  - [x] `CreateRoomAction`, `UpdateRoomAction`, `ToggleRoomAvailabilityAction`.
  - [x] `UploadListingImageAction`.
  - [x] DTOs: `ListingData`, `RoomData`.
  - [x] Repository: `ListingRepositoryInterface` & `EloquentListingRepository`.
- [x] **Task 1.3: HTTP Layer (Thin Controllers & Requests)**
  - [x] FormRequests: `StoreListingRequest`, `UpdateListingRequest`, `StoreRoomRequest`, `UploadImageRequest`.
  - [x] Policies: `ListingPolicy`, `RoomPolicy`.
  - [x] Controllers: `Public\ListingController`, `Owner\ListingController`, `Owner\RoomController`, `Owner\ListingImageController`.
  - [x] Routes: `/kos`, `/kos/{slug}`, `/owner/kos/*`.
- [x] **Task 1.4: Frontend Pages & Components (React 19 + Inertia v3 + Tailwind v4 + Taste Skill)**
  - [x] UI Primitives: `Button.tsx`, `Badge.tsx`, `Card.tsx`, `Input.tsx`, `Select.tsx`, `Modal.tsx`.
  - [x] Feature Components: `ListingCard.tsx`, `RoomCard.tsx`, `ImageGallery.tsx`, `FacilityBadge.tsx`.
  - [x] Public Pages: `resources/js/Pages/Listings/Index.tsx`, `resources/js/Pages/Listings/Show.tsx`.
  - [x] Owner Pages: `resources/js/Pages/Owner/Listings/Index.tsx`, `resources/js/Pages/Owner/Listings/Create.tsx`, `resources/js/Pages/Owner/Listings/Edit.tsx`, `resources/js/Pages/Owner/Rooms/Index.tsx`.
- [x] **Task 1.5: Testing & Verification**
  - [x] Unit Tests: `ListingModelTest`, `RoomModelTest`, `CreateListingActionTest`, `CreateRoomActionTest`, `PublishListingActionTest`, `UploadListingImageActionTest`.
  - [x] Feature Tests: `PublicListingTest`, `OwnerListingManagementTest`.
  - [x] Quality Gate:
    - [x] `composer validate` -> Valid
    - [x] `./vendor/bin/pint --test` -> Passed
    - [x] `./vendor/bin/phpstan analyse` -> [OK] No errors (Level Max)
    - [x] `php artisan test` -> 24 passed (64 assertions)
    - [x] `npx tsc --noEmit` -> 0 errors
    - [x] `npm run lint` -> Passed
    - [x] `npm run build` -> Passed (3.72s)
    - [x] Graphify AST: 475 nodes, 886 edges, 33 communities.

---

## ✅ Milestone 2: Modul Pemesanan & Pembayaran
**Status:** `COMPLETED` 🟢 (Verified 2026-09-01)  
**Bounded Context:** `App\Domain\Booking\`

- [x] **Task 2.1: Database Schema & Anti-Double-Booking Locking**
  - [x] Migration `create_bookings_table` (UUID PK, booking_code unique, user_id, room_id, listing_id, start_date, duration_months, end_date, total_amount BIGINT, deposit_amount BIGINT, grand_total BIGINT, status enum, payment_status enum, created_at, expired_at).
  - [x] Migration `create_payments_table` (UUID PK, booking_id, invoice_number, payment_channel, payment_reference, gross_amount BIGINT, transaction_status, payload, paid_at).
  - [x] Migration `create_room_status_audits_table` (UUID PK, room_id, booking_id, user_id, previous_status, new_status, previous_available_count, new_available_count, reason).
  - [x] Enums: `BookingStatus` (`pending`, `approved`, `rejected`, `cancelled`, `expired`, `completed`), `PaymentStatus` (`pending`, `paid`, `failed`, `expired`, `cancelled`).
  - [x] Models: `Booking.php`, `Payment.php`, `RoomStatusAudit.php`, updated `Room.php`, `Listing.php`, `User.php`.
  - [x] Exception: `RoomUnavailableException.php`.
- [x] **Task 2.2: Booking Flow & Concurrency Lock (Action Pattern)**
  - [x] `CreateBookingAction`: `DB::transaction()` + `Room::lockForUpdate()` + availability guard + integer Rupiah calculation + room inventory decrement + audit log.
  - [x] `ApproveBookingAction`: State transition guard (`pending` -> `approved`).
  - [x] `RejectBookingAction`: Atomically restores room availability with lock & audit record.
  - [x] `CancelBookingAction`: Idempotent cancellation + restores room inventory with lock & audit record.
  - [x] `ProcessPaymentAction`: Idempotent settlement payment + updates booking to paid.
  - [x] `ExpireBookingsAction` & `ExpireBookingsCommand`: Auto-expiration of unpaid bookings after 24h window.
  - [x] Repository: `BookingRepositoryInterface` & `EloquentBookingRepository`.
- [x] **Task 2.3: HTTP Layer (Controllers, FormRequests, Policies & Routes)**
  - [x] FormRequests: `StoreBookingRequest`, `SimulatePaymentRequest`.
  - [x] Policy: `BookingPolicy` (authorization guards).
  - [x] Controllers: `BookingController`, `PaymentController`, `Owner\BookingManagementController`.
  - [x] Routes: `/booking/{room}/checkout`, `/booking`, `/booking/{id}`, `/booking/{id}/cancel`, `/booking/{id}/payment`, `/owner/bookings/*`.
- [x] **Task 2.4: Frontend Booking & Payment UI (React 19 + Inertia v3 + Taste Skill)**
  - [x] `resources/js/types/booking.ts`.
  - [x] Updated `resources/js/Pages/Listings/Show.tsx` (Direct room selection to checkout).
  - [x] `resources/js/Pages/Booking/Checkout.tsx` (Real-time zero float integer calculation & tenant info).
  - [x] `resources/js/Pages/Booking/Show.tsx` (24h countdown timer, Virtual Account simulator, instant payment & cancel actions).
  - [x] `resources/js/Pages/Booking/Success.tsx` (Confirmation screen).
  - [x] `resources/js/Pages/Owner/Bookings/Index.tsx` (Owner approval dashboard with status tabs, approve and reject modals).
- [x] **Task 2.5: Testing & Concurrency Verification**
  - [x] `BookingModelTest.php` (UUID, pure integer formatting, relations).
  - [x] `CreateBookingActionTest.php` (Actions, invariants, inventory restore).
  - [x] `ConcurrencyLockTest.php` (Real competing request lock test, zero double booking).
  - [x] `BookingFlowTest.php` (Checkout, store, show, pay, cancel HTTP flows).
  - [x] `OwnerBookingApprovalTest.php` (Owner approval & rejection flows).
  - [x] Quality Gate:
    - [x] `composer validate` -> Valid
    - [x] `./vendor/bin/pint --test` -> Passed
    - [x] `composer phpstan` -> [OK] No errors (Level Max)
    - [x] `php artisan test` -> 35 passed (130 assertions)
    - [x] `npx tsc --noEmit` -> 0 errors
    - [x] `npm run lint` -> Passed
    - [x] `npm run build` -> Passed (4.44s)
    - [x] Graphify AST: 625 nodes, 1329 edges, 37 communities.

---

## ✅ Milestone 3: Modul Pencarian & Filter Cerdas
**Status:** `COMPLETED` 🟢 (Verified 2026-09-01)  
**Bounded Context:** `App\Domain\Search\`

- [x] **Task 3.1: Search Domain & Database Fallback Architecture**
  - [x] DTO `SearchCriteria.php` (strict keyword, gender, area, minPrice, maxPrice, facilityIds, sort, perPage).
  - [x] Repository `SearchRepositoryInterface.php` & `EloquentSearchRepository.php` (Zero Float integer calculations, faceted multi-attribute filtering, subquery sorting, N+1 eager loading).
  - [x] Action `SearchListingsAction.php`.
  - [x] Service provider binding in `AppServiceProvider.php`.
- [x] **Task 3.2: HTTP Layer & FormRequests**
  - [x] FormRequest `SearchListingRequest.php` (validated faceted criteria mapping).
  - [x] Updated `Public\ListingController.php` with full filter state response.
- [x] **Task 3.3: Frontend Faceted Search UI (React 19 + Taste Skill)**
  - [x] `resources/js/features/search/components/FilterModal.tsx` (Faceted budget range inputs, quick chips, facility checkboxes multi-select, sort options).
  - [x] Updated `resources/js/features/listings/components/AirbnbSearchBar.tsx` (Keyword input, area select, gender popover, Coral search button).
  - [x] Updated `resources/js/features/listings/components/FilterBar.tsx` (Quick gender pills, sort select, FilterModal trigger with active badge count, active tag chips, reset button).
  - [x] Updated `resources/js/Pages/Listings/Index.tsx` with seamless modal integration and responsive 3-column layout.
- [x] **Task 3.4: Testing & Verification**
  - [x] `SearchListingsActionTest.php` (Keyword search, gender filter, area filter, budget range, and facility intersection).
  - [x] `ListingSearchFilterTest.php` (HTTP Inertia feature tests for full search criteria).
  - [x] Quality Gate:
    - [x] `composer validate` -> Valid
    - [x] `./vendor/bin/pint --test` -> Passed
    - [x] `composer phpstan` -> [OK] No errors (Level Max, 88 files analyzed)
    - [x] `php artisan test` -> 39 passed (170 assertions)
    - [x] `npx tsc --noEmit` -> 0 errors
    - [x] `npm run lint` -> Passed
    - [x] `npm run build` -> Passed (3.86s)
    - [x] Graphify AST: 672 nodes, 1456 edges, 40 communities.

---

## ✅ Milestone 4: Modul Review, Rating & Wishlist
**Status:** `COMPLETED` 🟢 (Verified 2026-09-01)  
**Bounded Contexts:** `App\Domain\Review\`, `App\Domain\Wishlist\`

- [x] **Task 4.1: Database Schema & Invariants**
  - [x] Migration `create_reviews_table` (UUID PK, user_id, listing_id, booking_id, rating 1-5, comment, owner_reply, owner_replied_at, soft deletes, unique user_id + listing_id).
  - [x] Migration `create_wishlists_table` (UUID PK, user_id, listing_id, unique user_id + listing_id).
  - [x] Models: `Review.php` & `Wishlist.php` (HasUuids, relations to User and Listing).
  - [x] Relations on `User.php` & `Listing.php` (`reviews()`, `wishlists()`).
- [x] **Task 4.2: Domain Actions & Repositories**
  - [x] `CreateReviewAction` (Transaction + triggers `RecalculateListingRatingAction`).
  - [x] `RecalculateListingRatingAction` (Atomically updates `rating_avg` and `review_count` on Listing).
  - [x] `ReplyReviewAction` (Owner reply timestamp and text).
  - [x] `ToggleWishlistAction` (Instant toggle between added/removed).
  - [x] Repositories: `ReviewRepositoryInterface`, `EloquentReviewRepository`, `WishlistRepositoryInterface`, `EloquentWishlistRepository`.
  - [x] Bindings in `AppServiceProvider.php`.
- [x] **Task 4.3: HTTP Layer & FormRequests**
  - [x] FormRequests: `StoreReviewRequest`, `ReplyReviewRequest`, `ToggleWishlistRequest`.
  - [x] Controllers: `ReviewController` (`POST /kos/{slug}/reviews`, `POST /reviews/{id}/reply`) and `WishlistController` (`GET /wishlist`, `POST /wishlist/toggle`).
  - [x] Routes registered in `routes/web.php`.
  - [x] Eager-loading in `EloquentListingRepository::findBySlug` (`reviews.user`).
- [x] **Task 4.4: Frontend UI (React 19 + Taste Skill)**
  - [x] `resources/js/types/review.ts` & `wishlist.ts`.
  - [x] `ReviewList.tsx` (Star distribution, verified tenant badge, owner reply boxes, relative dates).
  - [x] `ReviewForm.tsx` (Interactive 1-5 star selector with live label and review comment textarea).
  - [x] `resources/js/Pages/Wishlist/Index.tsx` (Saved favorites grid with empty state).
  - [x] Updated `resources/js/Pages/Listings/Show.tsx` (Integrated ReviewList, ReviewForm, and Wishlist save button).
  - [x] Updated `resources/js/features/listings/components/ListingCard.tsx` (Wishlist heart toggle).
  - [x] Updated `resources/js/components/shared/PublicLayout.tsx` (Added Wishlist navigation link).
- [x] **Task 4.5: Testing & Verification**
  - [x] `ReviewModelTest.php`, `WishlistModelTest.php`.
  - [x] `CreateReviewActionTest.php`, `ToggleWishlistActionTest.php`.
  - [x] `ReviewFlowTest.php`, `WishlistFlowTest.php`.
  - [x] Quality Gate:
    - [x] `composer validate` -> Valid
    - [x] `./vendor/bin/pint --test` -> Passed
    - [x] `composer phpstan` -> [OK] No errors (Level Max, 106 files analyzed)
    - [x] `php artisan test` -> 46 passed (210 assertions)
    - [x] `npx tsc --noEmit` -> 0 errors
    - [x] `npm run lint` -> Passed
    - [x] `npm run build` -> Passed (3.97s)
    - [x] Graphify AST: 782 nodes, 1778 edges, 58 communities.

---

## ✅ Milestone 5: Autentikasi, RBAC & Notifikasi
**Status:** `COMPLETED` 🟢 (Verified 2026-09-01)  
**Bounded Contexts:** `App\Domain\User\`, `App\Domain\Notification\`

- [x] **Task 5.1: RBAC & Permissions (Spatie)**
  - [x] Spatie Laravel Permission integration with migration `2026_09_01_140042_create_permission_tables.php`.
  - [x] Enum `UserRole` (`admin`, `owner`, `tenant`).
  - [x] Seed roles & permissions: `manage-listings`, `manage-rooms`, `approve-bookings`, `moderate-reviews`, `manage-users`, `book-rooms`, `submit-reviews`, `save-wishlists`.
  - [x] Laravel Policies: `ListingPolicy`, `RoomPolicy`, `BookingPolicy`, `ReviewPolicy`.
  - [x] Zero hardcoded role checks in controllers.
- [x] **Task 5.2: Multi-Role Authentication Flow**
  - [x] Auth pages: `Login.tsx` (with demo autofill chips for owner & student) and `Register.tsx` (interactive role tabs: Pencari Kos vs Pemilik Kos).
  - [x] `AuthenticatedSessionController` (`GET /login`, `POST /login`, `POST /logout`).
  - [x] `RegisteredUserController` (`GET /register`, `POST /register` with Spatie role assignment).
  - [x] Session regeneration and rate limiting.
- [x] **Task 5.3: In-App & Multi-Channel Notifications**
  - [x] Migration `create_notifications_table.php` (UUID PK, notifiable morphs, data jsonb).
  - [x] Notification classes: `BookingCreatedNotification`, `BookingStatusChangedNotification`, `PaymentConfirmedNotification`.
  - [x] `NotificationBell.tsx` (Real-time notification bell in navbar with animated badge counter, popover drawer, and mark all as read action).
  - [x] `NotificationController` (`GET /notifications`, `POST /notifications/{id}/read`, `POST /notifications/read-all`).
- [x] **Task 5.4: User Profile & Security Settings**
  - [x] `resources/js/Pages/Profile/Edit.tsx` (Personal information, WhatsApp phone number, and password update).
  - [x] `ProfileController` (`GET /profile`, `PUT /profile`, `PUT /profile/password`).
  - [x] User menu dropdown in `PublicLayout.tsx` with role badge and logout button.
- [x] **Task 5.5: Testing & Verification**
  - [x] `UserRoleTest.php`, `PolicyAccessControlTest.php`.
  - [x] `AuthFlowTest.php`, `NotificationFlowTest.php`.
  - [x] Quality Gate:
    - [x] `composer validate` -> Valid
    - [x] `./vendor/bin/pint --test` -> Passed
    - [x] `composer phpstan` -> [OK] No errors (Level Max, 141 files analyzed)
    - [x] `php artisan test` -> 58 passed (266 assertions)
    - [x] `npx tsc --noEmit` -> 0 errors (Strict mode)
    - [x] `npm run lint` -> Passed (0 errors/warnings)
    - [x] `npm run build` -> Passed (3.29s)
    - [x] Graphify AST: 956 nodes, 2193 edges, 65 communities.

---

## ✅ Milestone 6: Polishing, Observability, Error Boundaries & Health Monitoring
**Status:** `COMPLETED` 🟢 (Verified 2026-09-02)

- [x] **Task 6.1: Observability & Health Checks**
  - [x] Endpoint `GET /health` (`HealthCheckController` mengecek PostgreSQL connection latency, Redis ping latency, dan storage disk availability).
  - [x] Correlation ID Middleware (`CorrelationIdMiddleware` dengan `X-Request-Id` header pada setiap request & response).
  - [x] Contextual structured logging (`Log::withContext(['correlation_id' => $id])`).
- [x] **Task 6.2: Frontend UX Polish & Error Handling**
  - [x] React Error Boundaries pada layout (`ErrorBoundary.tsx` dengan fallback UI ramah).
  - [x] Custom Inertia Error page (`Error.tsx` untuk status 404, 403, 500, 503).
  - [x] Floating Toast notification system (`Toast.tsx` dengan hook `useToast()` dan auto flash message listener).
  - [x] Calibrated brand Logo with tight transparent cropping and clear typography.
- [x] **Task 6.3: Automated Feature Testing**
  - [x] `HealthCheckTest.php` (Memverifikasi payload JSON `/health` dan respons 503 saat DB offline).
  - [x] `CorrelationIdTest.php` (Memverifikasi pembentukan dan preservasi header `X-Request-Id`).
  - [x] `ErrorPageTest.php` (Memverifikasi status 404 dan redirect auth pada dashboard).
- [x] **Task 6.4: Final Quality Gate & Production Readiness**
  - [x] Laravel Pint: `passed` (100% PSR-12 strict).
  - [x] PHPStan Level Max: `138/138 files [OK] No errors`.
  - [x] TypeScript Strict: `0 errors`.
  - [x] ESLint: `0 errors / 0 warnings`.
  - [x] Vite Build: `Compiled in 4.33s`.
  - [x] Pest Test Suite: `85 passed (359 assertions)`.

---


---

## ✅ Milestone 7: AI Engineering Showcase — NESA Agentic RAG Chatbot & Data Seed Enrichment
**Status:** COMPLETED 🟢 (Verified 2026-10-16)  
**Bounded Context:** App\\Domain\\Ai\\

- [x] **Task 7.1: Data Dummy & Seed Enrichment**
  - [x] ListingSeeder.php diperkaya dengan 19 kos nyata di seluruh lingkar kampus UNNES.
  - [x] 19 Akun pengguna (1 admin, 7 owner terverifikasi, 1 owner pending, 10 mahasiswa UNNES).
  - [x] 32 Tipe kamar, 95 galeri foto lengkap, 11 ulasan mahasiswa riil, 13 wishlist.
  - [x] 13 Booking, 10 Pembayaran, 9 Review Verifikasi, 4 Payout.
- [x] **Task 7.2: Database Schema & AI Models**
  - [x] Migration create_nesa_chat_tables (nesa_conversations & nesa_messages).
  - [x] Model NesaConversation & NesaMessage dengan jsonb casts.
  - [x] Config config/ai.php & .env.example.
- [x] **Task 7.3: LLM Client & Tool Calling Service (Ponytail Discipline)**
  - [x] NesaToolService: Schema OpenAI function calling untuk search_kos, get_kos_detail, get_campus_guide.
  - [x] NesaLlmService: Provider-agnostic HTTP client via native Http (0 new packages).
  - [x] Fault tolerance: Graceful error & timeout fallback.
- [x] **Task 7.4: Agentic Action & Multi-Turn Orchestration**
  - [x] SendNesaMessageAction: Multi-turn sliding memory, tool execution loop, grounded facts.
  - [x] Graceful fallback: Otomatis mencari listing relevan saat API LLM offline.
  - [x] Hydration data listing cards terstruktur (recommended_listings).
- [x] **Task 7.5: HTTP API Endpoints**
  - [x] NesaChatController (POST /api/nesa/chat & GET /api/nesa/history).
  - [x] Rate limiting throttle:60,1 dan validasi FormRequest.
- [x] **Task 7.6: Frontend Floating Widget & Interactive Cards (Taste Compliant)**
  - [x] NesaWidget.tsx: Floating action button, responsive drawer, glowing online badge.
  - [x] Quick prompt chips dan interactive listing cards.
  - [x] Strict Zero Axios: Menggunakan window.fetch native browser dengan proteksi CSRF.
  - [x] Terintegrasi di PublicLayout.tsx.
- [x] **Task 7.7: Full Quality Gate & DoD Verification**
  - [x] Pest Test Suite: 140 passed (743 assertions) (26 test khusus NESA).
  - [x] Static Analysis (PHPStan Level Max): [OK] No errors across 189 files.
  - [x] Laravel Pint: passed.
  - [x] TypeScript Strict: 0 errors (npx tsc --noEmit).
  - [x] ESLint: 0 errors, 0 warnings (npm run lint).
  - [x] Production Asset Build: npm run build berhasil.
  - [x] Ponytail Compliance: 0 Composer packages baru, 0 npm packages baru.

## 📌 Aturan Pembaruan Dokumen
1. Setiap kali task selesai dikerjakan dan diverifikasi dengan test riil, ubah status `[ ]` menjadi `[x]`.
2. Dilarang menandai task selesai jika verifikasi perintah belum dijalankan.
3. Catat tanggal penyelesaian pada setiap milestone yang selesai.
