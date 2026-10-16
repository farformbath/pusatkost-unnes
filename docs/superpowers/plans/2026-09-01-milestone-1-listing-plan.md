# Milestone 1: Modul Listing & Manajemen Kamar Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Membangun subsistem katalog kos dan kamar lengkap pada Bounded Context `App\Domain\Listing\` (PostgreSQL skema, DTO, Action Pattern, Repository, HTTP Controller & Policy, FormRequest, React 19 UI, Pest Tests, dan Image Upload).

**Architecture:** Modular Monolith DDD-lite dengan Action Pattern untuk setiap mutasi bisnis, UUID primary keys, BIGINT Rupiah invariants, Eloquent Strict Mode, Inertia.js v3, React 19, Tailwind CSS v4, dan zero float.

**Tech Stack:** PHP 8.4+, Laravel 12/13, PostgreSQL 16, Inertia.js v3, React 19, TypeScript Strict, Tailwind CSS v4, Pest v3, PHPStan Level Max.

**Spec:** [`docs/superpowers/specs/2026-09-01-milestone-1-listing-design.md`](file:///c:/projects/unneskost2/docs/superpowers/specs/2026-09-01-milestone-1-listing-design.md)

---

## Global Constraints
- `declare(strict_types=1);` di setiap berkas PHP baru.
- Seluruh nilai moneter (`price_monthly`, `price_deposit`) disimpan dalam tipe `BIGINT` integer Rupiah bulat (Zero Float).
- Tidak menggunakan Axios; routing dan form menggunakan helper native Inertia.js v3.
- TypeScript strict mode (`strict=true`, `noImplicitAny=true`, 0 `any`).
- Dilarang menaruh logika bisnis di controller (Controller memanggil FormRequest $\rightarrow$ 1 Action $\rightarrow$ Response).
- Taste Skill diterapkan khusus pada frontend UI komponen dan halaman React.

---

## Task Breakdown

### Task 0: Pembersihan Cepat Temuan Ponytail-Review
- [ ] Hapus `resources/views/welcome.blade.php` (277 baris dead code).
- [ ] Sederhanakan `app/Http/Middleware/HandleInertiaRequests.php` (hapus override `version()` yang redundan).
- [ ] Hapus dummy test `tests/Unit/ExampleTest.php`.
- [ ] Rapikan export pada `resources/js/components/shared/Logo.tsx`.
- [ ] **Verification:** `./vendor/bin/pint --test`, `php artisan test`, `npx tsc --noEmit`.

### Task 1: Database Migrations, Enums, Models & Seeders
- [ ] Buat Enums: `GenderType.php`, `ListingStatus.php`, `RoomStatus.php`.
- [ ] Buat Migrasi PostgreSQL 16:
  - `create_facilities_table` (UUID, name, slug, icon, category).
  - `create_listings_table` (UUID, owner_id FK, name, slug unique, gender_type, address, area, lat, long, rules, status, rating_avg, review_count).
  - `create_rooms_table` (UUID, listing_id FK, name, size, price_monthly BIGINT, price_deposit BIGINT, status, total_rooms, available_rooms).
  - `create_listing_images_table` (UUID, listing_id FK, room_id FK nullable, file_path, is_primary, sort_order).
  - `create_facility_listing_and_room_pivots_table`.
- [ ] Buat Eloquent Models: `Facility`, `Listing`, `Room`, `ListingImage` (dengan `HasUuids`, cast enums, scopes, relations).
- [ ] Buat `FacilitySeeder` (fasilitas populer sekitar UNNES: WiFi, AC, Kamar Mandi Dalam, Kasur, Lemari, Parkir Motor, Dapur Bersama).
- [ ] **Verification:** `php artisan migrate:fresh --seed`, Pest unit test relasi & model.

### Task 2: Domain DTOs, Action Pattern & Repository
- [ ] Buat DTOs: `ListingData.php`, `RoomData.php`.
- [ ] Buat Domain Actions di `app/Domain/Listing/Actions/`:
  - `CreateListingAction.php` (generate unique slug, persist listing, attach facilities).
  - `UpdateListingAction.php` (update detail & sync facilities).
  - `PublishListingAction.php` (validasi minimal 1 kamar & 1 foto utama).
  - `CreateRoomAction.php` (persist room with BIGINT validation, attach room facilities).
  - `UpdateRoomAction.php` & `ToggleRoomAvailabilityAction.php`.
  - `UploadListingImageAction.php` (save to storage/public, update primary flag).
- [ ] Buat Repository: `ListingRepositoryInterface.php` & `EloquentListingRepository.php`.
- [ ] Daftarkan repository binding di `AppServiceProvider.php`.
- [ ] **Verification:** Pest unit test untuk setiap Action (`CreateListingActionTest`, `RoomActionTest`, dll).

### Task 3: HTTP Layer (Controllers, FormRequests, Policies)
- [ ] Buat FormRequests: `StoreListingRequest`, `UpdateListingRequest`, `StoreRoomRequest`, `UploadImageRequest`.
- [ ] Buat Policies: `ListingPolicy.php`, `RoomPolicy.php` (otorisasi owner_id === auth()->id() atau admin).
- [ ] Buat Controllers:
  - `Public\ListingController.php` (`index`, `show`).
  - `Owner\ListingController.php` (`index`, `create`, `store`, `edit`, `update`, `publish`).
  - `Owner\RoomController.php` (`index`, `store`, `update`, `toggleAvailability`).
- [ ] Daftarkan routes di `routes/web.php`.
- [ ] **Verification:** Feature tests HTTP endpoints (`ListingFeatureTest`, `OwnerListingCrudTest`).

### Task 4: Frontend Components & Pages (React 19 + Inertia v3 + Taste Skill)
- [ ] Buat UI Primitives di `resources/js/components/ui/`: `Button.tsx`, `Badge.tsx`, `Card.tsx`, `Input.tsx`, `Modal.tsx`, `Select.tsx`.
- [ ] Buat Feature Components di `resources/js/features/listings/components/`:
  - `ListingCard.tsx` (thumbnail, badge gender, harga mulai dari, lokasi, rating).
  - `RoomCard.tsx` (detail ukuran, fasilitas kamar, harga bulanan, tombol booking/ketersediaan).
  - `ImageGallery.tsx` (grid preview galeri foto responsif + modal zoom).
  - `FacilityBadge.tsx` (icon + nama fasilitas).
- [ ] Buat Halaman Publik:
  - `resources/js/Pages/Listings/Index.tsx` (Katalog kos dengan grid responsif).
  - `resources/js/Pages/Listings/Show.tsx` (Halaman detail kos lengkap, galeri, daftar kamar).
- [ ] Buat Halaman Owner:
  - `resources/js/Pages/Owner/Listings/Index.tsx` (Tabel kos pemilik).
  - `resources/js/Pages/Owner/Listings/Create.tsx` & `Edit.tsx` (Form pendaftaran kos + uploader gambar).
  - `resources/js/Pages/Owner/Rooms/Index.tsx` (Manajemen kamar kos).
- [ ] **Verification:** `npx tsc --noEmit` bersih (0 error), `npm run lint`, `npm run build`.

### Task 5: Full Verification Gate & Milestone 1 Completion
- [ ] `composer validate`
- [ ] `./vendor/bin/pint --test`
- [ ] `./vendor/bin/phpstan analyse` (Level Max)
- [ ] `php artisan test` (Pest full suite hijau)
- [ ] `npx tsc --noEmit` (0 error)
- [ ] `npm run lint` (0 error)
- [ ] `npm run build` (Vite build sukses)
- [ ] Update `tasks.md` (tandai M1 selesai).
