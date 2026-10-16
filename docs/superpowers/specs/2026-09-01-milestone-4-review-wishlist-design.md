# 📐 Design Specification: Milestone 4 — Modul Review, Rating & Wishlist

- **Tanggal:** 2026-09-01
- **Bounded Contexts:** `App\Domain\Review\`, `App\Domain\Wishlist\`
- **Status:** In Progress
- **Referensi Kebutuhan:** [`requirements.md`](file:///c:/projects/unneskost2/requirements.md) (FR-REV-001 s.d. FR-REV-006, FR-WSH-001 s.d. FR-WSH-003).

---

## 1. Arsitektur Domain & Boundary

### A. Bounded Context `App\Domain\Review\`
Menangani ulasan testimoni, rating bintang skala 1-5, agregasi otomatis rating pada properti kos, dan balasan (*reply*) dari pemilik kos.

```
app/Domain/Review/
├── Actions/
│   ├── CreateReviewAction.php
│   ├── ReplyReviewAction.php
│   └── RecalculateListingRatingAction.php
├── DTOs/
│   └── ReviewData.php
├── Models/
│   └── Review.php
├── Policies/
│   └── ReviewPolicy.php
└── Repositories/
    ├── ReviewRepositoryInterface.php
    └── EloquentReviewRepository.php
```

### B. Bounded Context `App\Domain\Wishlist\`
Menangani penyimpanan properti favorit (*bookmark/wishlist*) per pengguna dengan toggle instan.

```
app/Domain/Wishlist/
├── Actions/
│   └── ToggleWishlistAction.php
├── Models/
│   └── Wishlist.php
└── Repositories/
    ├── WishlistRepositoryInterface.php
    └── EloquentWishlistRepository.php
```

---

## 2. Database Schema & Invariants

### A. Tabel `reviews`
- `id` (UUID PK)
- `user_id` (UUID FK $\rightarrow$ `users.id`, onDelete cascade)
- `listing_id` (UUID FK $\rightarrow$ `listings.id`, onDelete cascade)
- `booking_id` (UUID FK $\rightarrow$ `bookings.id`, nullable, onDelete set null)
- `rating` (INT, 1 s.d. 5)
- `comment` (TEXT)
- `owner_reply` (TEXT, nullable)
- `owner_replied_at` (TIMESTAMP, nullable)
- `created_at`, `updated_at`, `deleted_at`
- Unique constraint: `['user_id', 'listing_id']` (1 ulasan per kos per pengguna).

### B. Tabel `wishlists`
- `id` (UUID PK)
- `user_id` (UUID FK $\rightarrow$ `users.id`, onDelete cascade)
- `listing_id` (UUID FK $\rightarrow$ `listings.id`, onDelete cascade)
- `created_at`, `updated_at`
- Unique constraint: `['user_id', 'listing_id']`.

### C. Invariant Agregasi Rating
Setiap kali review dibuat, diubah, atau dihapus:
1. Hitung total review: `$reviewCount = $listing->reviews()->count();`
2. Hitung rata-rata: `$ratingAvg = $reviewCount > 0 ? (float) $listing->reviews()->avg('rating') : 0.0;`
3. Update listing: `$listing->update(['rating_avg' => round($ratingAvg, 2), 'review_count' => $reviewCount]);`

---

## 3. Frontend Experience (React 19 + Taste Skill)

1. **Heart Wishlist Toggle (`ListingCard.tsx` & `Show.tsx`)**:
   - Klik tombol hati memicu `POST /wishlist/toggle` dengan optimistik UI toggle.
2. **Halaman Wishlist Pengguna (`resources/js/Pages/Wishlist/Index.tsx`)**:
   - Grid kos yang disimpan dengan status ketersediaan dan tautan langsung.
3. **Komponen Ulasan (`ReviewList.tsx` & `ReviewForm.tsx`)**:
   - Ringkasan rating bintang besar, distribusi rating (5★, 4★, 3★, 2★, 1★).
   - Daftar ulasan dengan lencana *Penyewa Terverifikasi UNNES*, tanggal relatif, dan balasan resmi pemilik kos.
   - Form pembuatan review interaktif (bintang 1-5 dan textarea pesan).
