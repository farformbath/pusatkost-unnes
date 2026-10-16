# 📐 Design Specification: Milestone 1 — Modul Listing & Manajemen Kamar

- **Tanggal:** 2026-09-01
- **Bounded Context:** `App\Domain\Listing\`
- **Status:** Approved
- **Referensi Kebutuhan:** [`requirements.md`](file:///c:/projects/unneskost2/requirements.md) (FR-LIST-001 s.d. FR-LIST-008, FR-ROOM-001 s.d. FR-ROOM-005).

---

## 1. Arsitektur Domain & Boundary

Bounded Context `App\Domain\Listing\` bertanggung jawab penuh atas daur hidup kos dan kamar, terisolasi dari lapisan HTTP.

```
app/Domain/Listing/
├── Actions/
│   ├── CreateListingAction.php
│   ├── UpdateListingAction.php
│   ├── PublishListingAction.php
│   ├── CreateRoomAction.php
│   ├── UpdateRoomAction.php
│   ├── ToggleRoomAvailabilityAction.php
│   └── UploadListingImageAction.php
├── DTOs/
│   ├── ListingData.php
│   └── RoomData.php
├── Enums/
│   ├── GenderType.php        # PUTRA, PUTRI, CAMPUR
│   ├── ListingStatus.php     # DRAFT, PUBLISHED, ARCHIVED
│   └── RoomStatus.php        # AVAILABLE, OCCUPIED, MAINTENANCE
├── Models/
│   ├── Listing.php           # UUID PK, BelongsTo User (Owner), HasMany Rooms, Images
│   ├── Room.php              # UUID PK, BelongsTo Listing, BIGINT prices
│   ├── Facility.php          # Master fasilitas (icon, name, category)
│   └── ListingImage.php      # Foto kos & kamar, order, is_primary
└── Repositories/
    ├── ListingRepositoryInterface.php
    └── EloquentListingRepository.php
```

---

## 2. Skema Database PostgreSQL 16

### A. Tabel `facilities`
- `id` (UUID, PK)
- `name` (VARCHAR 100, Unique)
- `slug` (VARCHAR 100, Unique)
- `icon` (VARCHAR 50, nullable - Lucide icon name, e.g. `wifi`, `air-vent`, `bath`)
- `category` (VARCHAR 50 - `room`, `property`, `general`)
- `created_at`, `updated_at`

### B. Tabel `listings`
- `id` (UUID, PK)
- `owner_id` (UUID, FK -> `users.id`, Indexed)
- `name` (VARCHAR 255)
- `slug` (VARCHAR 255, Unique, Indexed)
- `gender_type` (VARCHAR 20 - `putra`, `putri`, `campur`)
- `description` (TEXT)
- `address` (TEXT)
- `area` (VARCHAR 100 - `Sekaran`, `Banaran`, `Taman Siswa`, dll)
- `latitude` (DECIMAL 10, 8, nullable)
- `longitude` (DECIMAL 11, 8, nullable)
- `rules` (TEXT, nullable)
- `status` (VARCHAR 20 - `draft`, `published`, `archived`, Indexed)
- `rating_avg` (DECIMAL 3, 2, Default 0.00)
- `review_count` (INTEGER, Default 0)
- `created_at`, `updated_at`, `deleted_at` (SoftDeletes)

### C. Tabel `rooms`
- `id` (UUID, PK)
- `listing_id` (UUID, FK -> `listings.id`, Cascading, Indexed)
- `name` (VARCHAR 100, e.g. `Tipe A Standard`, `Kamar 01`)
- `size` (VARCHAR 50, e.g. `3x4 m`)
- `price_monthly` (BIGINT - Rupiah integer bulat, Invariant $\ge 0$)
- `price_deposit` (BIGINT - Rupiah integer bulat, Default 0)
- `status` (VARCHAR 20 - `available`, `occupied`, `maintenance`, Indexed)
- `total_rooms` (INTEGER, Default 1)
- `available_rooms` (INTEGER, Default 1)
- `created_at`, `updated_at`, `deleted_at`

### D. Tabel `listing_images`
- `id` (UUID, PK)
- `listing_id` (UUID, FK -> `listings.id`, Cascading, Indexed)
- `room_id` (UUID, FK -> `rooms.id`, Nullable, Cascading, Indexed)
- `file_path` (VARCHAR 255)
- `is_primary` (BOOLEAN, Default false)
- `sort_order` (INTEGER, Default 0)
- `created_at`, `updated_at`

### E. Tabel Pivot `facility_listing` & `facility_room`
- `facility_id` (UUID, FK -> `facilities.id`, Cascading)
- `listing_id` / `room_id` (UUID, FK, Cascading)
- Primary Key composite `(facility_id, listing_id)` dan `(facility_id, room_id)`.

---

## 3. Alur Bisnis & Invariants

1. **Strict Zero Float:** Seluruh operasi harga kamar wajib integer Rupiah (`BIGINT`).
2. **Slug Generator:** Otomatis menghasilkan slug unik ramah SEO dari nama kos dan area.
3. **Availability Tracking:** `available_rooms` tidak boleh melebihi `total_rooms` dan tidak boleh negatif.
4. **Primary Image Invariant:** Setiap listing yang berstatus `published` harus memiliki minimal 1 foto utama (`is_primary = true`).
5. **Authorization:** Hanya pemilik kos (`owner_id === auth()->id()`) atau admin yang berhak mengedit/menghapus listing & kamar.

---

## 4. Antarmuka Pengguna (Frontend React 19 + Inertia v3)

1. **Halaman Publik:**
   - `resources/js/Pages/Listings/Index.tsx`: Daftar katalog kos dengan thumbnail, harga bulanan termurah, badge tipe gender, dan fasilitas.
   - `resources/js/Pages/Listings/Show.tsx`: Halaman detail kos dengan galeri foto, peta lokasi, fasilitas lengkap, peraturan kos, dan daftar tipe kamar yang tersedia.
2. **Halaman Manajemen Pemilik (Owner):**
   - `resources/js/Pages/Owner/Listings/Index.tsx`: Daftar kos milik owner dengan status draft/published.
   - `resources/js/Pages/Owner/Listings/Create.tsx` & `Edit.tsx`: Form multi-step pembuatan kos & upload gambar.
   - `resources/js/Pages/Owner/Rooms/Index.tsx`: Manajemen kamar & toggle ketersediaan instan.
