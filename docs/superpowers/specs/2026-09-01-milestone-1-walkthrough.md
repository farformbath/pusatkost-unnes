# 🚀 Walkthrough: Milestone 1 — Modul Listing & Manajemen Kamar Selesai

- **Milestone:** Milestone 1 (M1)
- **Bounded Context:** `App\Domain\Listing\`
- **Status:** `COMPLETED` 🟢
- **Tanggal Selesai:** 2026-09-01

---

## 📦 1. Apa yang Telah Diimplementasikan

### A. Database & Domain Models (`App\Domain\Listing\`)
- **Migrasi PostgreSQL 16:**
  - `facilities`: Master fasilitas kos/kamar ber-UUID.
  - `listings`: Data properti kos (UUID, slug unik, tipe gender, area UNNES, status draft/published, rating).
  - `rooms`: Data tipe kamar (UUID, harga bulanan & deposit bertipe `BIGINT` integer Rupiah murni, jumlah kamar & ketersediaan).
  - `listing_images`: Foto kos & kamar dengan status foto utama (`is_primary`) dan urutan (*sorting*).
  - `facility_listing` & `facility_room`: Pivot table relasional berindeks.
- **Eloquent Models:** `Facility`, `Listing`, `Room`, `ListingImage` dengan `HasUuids`, `SoftDeletes`, Enums, dan Scopes (`published`, `byGender`, `byArea`, `available`).
- **Enums:** `GenderType` (`putra`, `putri`, `campur`), `ListingStatus` (`draft`, `published`, `archived`), `RoomStatus` (`available`, `occupied`, `maintenance`).
- **Seeder:** `FacilitySeeder` berisi 15 fasilitas standar kampus UNNES.

### B. Action Pattern & Repositories
- `CreateListingAction`: Generate slug SEO unik otomatis + attach fasilitas.
- `UpdateListingAction`: Sinkronisasi pembaruan info kos + fasilitas.
- `PublishListingAction`: Validasi invariant wajib minimal 1 tipe kamar dan 1 foto utama.
- `CreateRoomAction` & `UpdateRoomAction`: Validasi invariant harga non-negatif & total kamar.
- `ToggleRoomAvailabilityAction`: Toggle status kamar instan (tersedia $\leftrightarrow$ penuh).
- `UploadListingImageAction`: Unggah gambar ke public disk dengan auto-primary selection.
- `ListingRepositoryInterface` & `EloquentListingRepository`: Pengambilan data dengan eager loading optimal untuk mencegah N+1 query.

### C. HTTP Layer & Policy
- **FormRequests:** `StoreListingRequest`, `UpdateListingRequest`, `StoreRoomRequest`, `UploadImageRequest`.
- **Policies:** `ListingPolicy`, `RoomPolicy` (otorisasi owner).
- **Controllers:**
  - `Public\ListingController`: Katalog publik (`/kos`) dan detail kos (`/kos/{slug}`).
  - `Owner\ListingController`: CRUD kos pemilik (`/owner/kos`, `/owner/kos/create`, `/owner/kos/{id}/edit`, `/owner/kos/{id}/publish`).
  - `Owner\RoomController`: Manajemen tipe kamar (`/owner/kos/{id}/kamar`, `/owner/kamar/{room}/toggle`).
  - `Owner\ListingImageController`: Unggah galeri foto kos.

### D. Frontend React 19 (Taste Skill & Anti-Slop)
- **UI Primitives:** `Button.tsx`, `Badge.tsx` (`GenderBadge`, `StatusBadge`), `Card.tsx`, `Input.tsx`, `Select.tsx`, `Modal.tsx`.
- **Feature Components:**
  - `ListingCard.tsx`: Thumbnail 4:3, floating gender badge, area tag, starting price Rp/bulan, dan facility chips.
  - `RoomCard.tsx`: Spesifikasi ukuran kamar (e.g. `3x4 m`), harga bulanan, deposit, fasilitas kamar, dan tombol ketersediaan.
  - `ImageGallery.tsx`: Layout hero foto grid 1-besar + 4-kecil (desktop) + Lightbox modal.
  - `FacilityBadge.tsx`: Tag fasilitas dengan icon SVG yang bersih.
- **Halaman Publik:**
  - `resources/js/Pages/Listings/Index.tsx`: Katalog kos lengkap dengan filter tipe gender & area sekitar UNNES, grid kartu, dan pagination.
  - `resources/js/Pages/Listings/Show.tsx`: Halaman detail kos dengan galeri foto, deskripsi, fasilitas lengkap, peraturan kos, daftar kamar, dan sticky quick-booking card.
- **Halaman Owner:**
  - `resources/js/Pages/Owner/Listings/Index.tsx`: Dashboard daftar kos pemilik.
  - `resources/js/Pages/Owner/Listings/Create.tsx`: Form pendaftaran kos baru.
  - `resources/js/Pages/Owner/Listings/Edit.tsx`: Form edit kos + dropzone uploader foto.
  - `resources/js/Pages/Owner/Rooms/Index.tsx`: Manajemen kamar & modal tambah kamar.

---

## 🧪 2. Bukti Hasil Verifikasi Mandiri (Verification Gate)

```
┌──────────────────────────────────────┬────────────────────────────┐
│ Checkpoint Pengujian                 │ Status & Hasil             │
├──────────────────────────────────────┼────────────────────────────┤
│ Composer Validate                    │ Valid (Exit 0)             │
│ Laravel Pint (Code Formatting)       │ Passed (Exit 0)            │
│ PHPStan Level Max (Static Analysis)  │ [OK] No errors (Exit 0)    │
│ Pest Testing Suite (Unit & Feature)  │ 24 passed (64 assertions)  │
│ TypeScript Type Check (tsc --noEmit) │ 0 error (Strict Mode)      │
│ ESLint 9 Flat Config                 │ Passed (0 error/warning)   │
│ Vite Production Asset Build          │ Built in 3.72s             │
│ Graphify Knowledge Graph             │ 475 nodes, 886 edges       │
└──────────────────────────────────────┴────────────────────────────┘
```

---

## 📁 3. Daftar Berkas Baru yang Dibuat

1. `app/Domain/Listing/DTOs/ListingData.php`
2. `app/Domain/Listing/DTOs/RoomData.php`
3. `app/Domain/Listing/Enums/GenderType.php`
4. `app/Domain/Listing/Enums/ListingStatus.php`
5. `app/Domain/Listing/Enums/RoomStatus.php`
6. `app/Domain/Listing/Models/Facility.php`
7. `app/Domain/Listing/Models/Listing.php`
8. `app/Domain/Listing/Models/Room.php`
9. `app/Domain/Listing/Models/ListingImage.php`
10. `app/Domain/Listing/Actions/CreateListingAction.php`
11. `app/Domain/Listing/Actions/UpdateListingAction.php`
12. `app/Domain/Listing/Actions/PublishListingAction.php`
13. `app/Domain/Listing/Actions/CreateRoomAction.php`
14. `app/Domain/Listing/Actions/UpdateRoomAction.php`
15. `app/Domain/Listing/Actions/ToggleRoomAvailabilityAction.php`
16. `app/Domain/Listing/Actions/UploadListingImageAction.php`
17. `app/Domain/Listing/Repositories/ListingRepositoryInterface.php`
18. `app/Domain/Listing/Repositories/EloquentListingRepository.php`
19. `app/Domain/Listing/Policies/ListingPolicy.php`
20. `app/Domain/Listing/Policies/RoomPolicy.php`
21. `app/Http/Controllers/Public/ListingController.php`
22. `app/Http/Controllers/Owner/ListingController.php`
23. `app/Http/Controllers/Owner/RoomController.php`
24. `app/Http/Controllers/Owner/ListingImageController.php`
25. `app/Http/Requests/Listing/StoreListingRequest.php`
26. `app/Http/Requests/Listing/UpdateListingRequest.php`
27. `app/Http/Requests/Listing/StoreRoomRequest.php`
28. `app/Http/Requests/Listing/UploadImageRequest.php`
29. `database/migrations/2026_09_01_000001_create_facilities_table.php`
30. `database/migrations/2026_09_01_000002_create_listings_table.php`
31. `database/migrations/2026_09_01_000003_create_rooms_table.php`
32. `database/migrations/2026_09_01_000004_create_listing_images_table.php`
33. `database/migrations/2026_09_01_000005_create_facility_pivots_table.php`
34. `database/seeders/FacilitySeeder.php`
35. `resources/js/types/listing.ts`
36. `resources/js/components/ui/Button.tsx`
37. `resources/js/components/ui/Badge.tsx`
38. `resources/js/components/ui/Card.tsx`
39. `resources/js/components/ui/Input.tsx`
40. `resources/js/components/ui/Select.tsx`
41. `resources/js/components/ui/Modal.tsx`
42. `resources/js/features/listings/components/FacilityBadge.tsx`
43. `resources/js/features/listings/components/ListingCard.tsx`
44. `resources/js/features/listings/components/RoomCard.tsx`
45. `resources/js/features/listings/components/ImageGallery.tsx`
46. `resources/js/Pages/Listings/Index.tsx`
47. `resources/js/Pages/Listings/Show.tsx`
48. `resources/js/Pages/Owner/Listings/Index.tsx`
49. `resources/js/Pages/Owner/Listings/Create.tsx`
50. `resources/js/Pages/Owner/Listings/Edit.tsx`
51. `resources/js/Pages/Owner/Rooms/Index.tsx`
52. `tests/Unit/Domain/Listing/ListingModelTest.php`
53. `tests/Unit/Domain/Listing/RoomModelTest.php`
54. `tests/Unit/Domain/Listing/Actions/CreateListingActionTest.php`
55. `tests/Unit/Domain/Listing/Actions/CreateRoomActionTest.php`
56. `tests/Unit/Domain/Listing/Actions/PublishListingActionTest.php`
57. `tests/Unit/Domain/Listing/Actions/UploadListingImageActionTest.php`
58. `tests/Feature/PublicListingTest.php`
59. `tests/Feature/OwnerListingManagementTest.php`
