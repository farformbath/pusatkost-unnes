# 🚀 Walkthrough: Milestone 4 — Modul Review, Rating & Wishlist Selesai

- **Milestone:** Milestone 4 (M4)
- **Bounded Contexts:** `App\Domain\Review\`, `App\Domain\Wishlist\`
- **Status:** `COMPLETED` 🟢
- **Tanggal Selesai:** 2026-09-01

---

## 📦 1. Apa yang Telah Diimplementasikan

### A. Review & Rating Domain (`App\Domain\Review\`)
- **Tabel `reviews`**:
  - UUID Primary Key (`id`), relasi ke `users`, `listings`, dan `bookings` (opsional).
  - Skala rating bintang 1 s.d. 5 (`unsignedTinyInteger`).
  - Ulasan teks (`comment`), balasan resmi pemilik (`owner_reply`), dan timestamp balasan (`owner_replied_at`).
  - Constraint unik: `['user_id', 'listing_id']` (1 ulasan per user per kos).
- **Agregasi Rating Otomatis (`RecalculateListingRatingAction`)**:
  - Menghitung ulang rata-rata bintang dan total review pada tabel `listings` secara atomik dalam satu transaksi saat review dibuat atau dihapus.
- **Actions**:
  - `CreateReviewAction`: Membuat / memperbarui ulasan dan memicu agregasi rating.
  - `ReplyReviewAction`: Menangani balasan pemilik kos atas ulasan penyewa.
- **Repository**: `ReviewRepositoryInterface` & `EloquentReviewRepository`.

### B. Wishlist & Bookmark Domain (`App\Domain\Wishlist\`)
- **Tabel `wishlists`**:
  - UUID PK, foreign key ke `users` dan `listings`, dengan constraint unik `['user_id', 'listing_id']`.
- **`ToggleWishlistAction`**:
  - Menambahkan ke favorit jika belum ada, atau menghapus dari favorit jika sudah ada (*instant toggle*).
- **Repository**: `WishlistRepositoryInterface` & `EloquentWishlistRepository`.

### C. HTTP & Controller Layer
- **`ReviewController.php`**:
  - `POST /kos/{slug}/reviews`: Validasi via `StoreReviewRequest` (rating 1-5, komentar min 5 karakter) $\rightarrow$ eksekusi `CreateReviewAction`.
  - `POST /reviews/{id}/reply`: Validasi via `ReplyReviewRequest` $\rightarrow$ eksekusi `ReplyReviewAction`.
- **`WishlistController.php`**:
  - `GET /wishlist`: Menampilkan daftar kos favorit pengguna tersimpan.
  - `POST /wishlist/toggle`: Validasi via `ToggleWishlistRequest` $\rightarrow$ eksekusi `ToggleWishlistAction`.

### D. Frontend React 19 UI (Taste Skill)
- **`ReviewList.tsx`**:
  - Ringkasan rating bintang besar (`4.8 / 5.0`), distribusi bintang, lencana *Penyewa Terverifikasi UNNES*.
  - Kotak balasan resmi pengelola kos (`💬 Balasan Pengelola Kos`).
  - Form balasan langsung bagi pemilik kos.
- **`ReviewForm.tsx`**:
  - Selector interaktif bintang 1-5 dengan efek hover dan live label (*Sangat Puas, Puas, Cukup, Kurang, Sangat Kurang*).
  - Textarea catatan pengalaman dan tombol kirim berstatus loading.
- **`Pages/Wishlist/Index.tsx`**:
  - Halaman khusus daftar kos favorit tersimpan dengan kartu kos 4:3, tombol hapus/toggle, dan tautan langsung ke checkout.
- **`ListingCard.tsx` & `Listings/Show.tsx`**:
  - Tombol hati ❤️ terintegrasi langsung dengan `POST /wishlist/toggle`.

---

## 🧪 2. Bukti Hasil Verifikasi Mandiri (*Verification Gate*)

```
┌──────────────────────────────────────┬─────────────────────────────┐
│ Checkpoint Pengujian                 │ Status & Hasil              │
├──────────────────────────────────────┼─────────────────────────────┤
│ Composer Validate                    │ Valid (Exit 0)              │
│ Laravel Pint (Code Formatting)       │ Passed (Exit 0)             │
│ PHPStan Level Max (Static Analysis)  │ 106/106 [OK] No errors      │
│ Pest Testing Suite (Unit & Feature)  │ 46 passed (210 assertions)  │
│ TypeScript Type Check (tsc --noEmit) │ 0 error (Strict Mode)       │
│ ESLint 9 Flat Config                 │ Passed (0 error/warning)    │
│ Vite Production Asset Build          │ Built in 3.97s              │
│ Graphify Knowledge Graph             │ 782 nodes, 1778 edges       │
└──────────────────────────────────────┴─────────────────────────────┘
```

---

## 📁 3. Daftar Berkas Baru & Modifikasi

1. `database/migrations/2026_09_01_000009_create_reviews_table.php`
2. `database/migrations/2026_09_01_000010_create_wishlists_table.php`
3. `app/Domain/Review/Models/Review.php`
4. `app/Domain/Review/DTOs/ReviewData.php`
5. `app/Domain/Review/Actions/RecalculateListingRatingAction.php`
6. `app/Domain/Review/Actions/CreateReviewAction.php`
7. `app/Domain/Review/Actions/ReplyReviewAction.php`
8. `app/Domain/Review/Repositories/ReviewRepositoryInterface.php`
9. `app/Domain/Review/Repositories/EloquentReviewRepository.php`
10. `app/Domain/Wishlist/Models/Wishlist.php`
11. `app/Domain/Wishlist/Actions/ToggleWishlistAction.php`
12. `app/Domain/Wishlist/Repositories/WishlistRepositoryInterface.php`
13. `app/Domain/Wishlist/Repositories/EloquentWishlistRepository.php`
14. `app/Http/Requests/Review/StoreReviewRequest.php`
15. `app/Http/Requests/Review/ReplyReviewRequest.php`
16. `app/Http/Requests/Wishlist/ToggleWishlistRequest.php`
17. `app/Http/Controllers/Review/ReviewController.php`
18. `app/Http/Controllers/Wishlist/WishlistController.php`
19. `resources/js/types/review.ts`
20. `resources/js/types/wishlist.ts`
21. `resources/js/features/reviews/components/ReviewList.tsx`
22. `resources/js/features/reviews/components/ReviewForm.tsx`
23. `resources/js/Pages/Wishlist/Index.tsx`
24. `tests/Unit/Domain/Review/ReviewModelTest.php`
25. `tests/Unit/Domain/Review/CreateReviewActionTest.php`
26. `tests/Unit/Domain/Wishlist/WishlistModelTest.php`
27. `tests/Unit/Domain/Wishlist/ToggleWishlistActionTest.php`
28. `tests/Feature/ReviewFlowTest.php`
29. `tests/Feature/WishlistFlowTest.php`
30. Modifikasi: `app/Models/User.php`, `app/Domain/Listing/Models/Listing.php`, `app/Domain/Listing/Repositories/EloquentListingRepository.php`, `app/Providers/AppServiceProvider.php`, `routes/web.php`, `resources/js/types/listing.ts`, `resources/js/components/shared/PublicLayout.tsx`, `resources/js/features/listings/components/ListingCard.tsx`, `resources/js/Pages/Listings/Show.tsx`.
