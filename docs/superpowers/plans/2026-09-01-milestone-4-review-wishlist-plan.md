# Milestone 4: Modul Review, Rating & Wishlist Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development or superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Membangun modul ulasan & rating bintang kredibel dengan agregasi otomatis (`App\Domain\Review\`) dan modul simpan kos favorit / wishlist (`App\Domain\Wishlist\`).

**Architecture:** Modular Monolith, Action Pattern, DTO, Repository Pattern, Inertia.js v3, React 19 Strict, Tailwind CSS v4, Pest, PHPStan Level Max.

**Spec:** [`docs/superpowers/specs/2026-09-01-milestone-4-review-wishlist-design.md`](file:///c:/projects/unneskost2/docs/superpowers/specs/2026-09-01-milestone-4-review-wishlist-design.md)

---

## Global Invariants
- `declare(strict_types=1);` pada seluruh berkas PHP baru.
- Strict Zero Axios.
- Strict TypeScript (`"strict": true`, 0 `any`).
- Invariant: Rating integer 1 s.d. 5, agregasi `rating_avg` dan `review_count` otomatis dan konsisten.

---

## Task Breakdown

### Task 1: Database Migrations, Models & Relationships (`Review` & `Wishlist`)
- [ ] Migration `2026_09_01_000009_create_reviews_table.php`.
- [ ] Migration `2026_09_01_000010_create_wishlists_table.php`.
- [ ] Model `App\Domain\Review\Models\Review.php`.
- [ ] Model `App\Domain\Wishlist\Models\Wishlist.php`.
- [ ] Update relasi di `User.php` dan `Listing.php` (`reviews()`, `wishlists()`).
- [ ] **Verification:** Pest Unit Tests (`ReviewModelTest.php`, `WishlistModelTest.php`).

### Task 2: Domain Actions, DTOs & Repositories
- [ ] DTO `ReviewData.php` (userId, listingId, bookingId, rating, comment).
- [ ] Action `CreateReviewAction.php` (Membuat review + trigger agregasi rating).
- [ ] Action `ReplyReviewAction.php` (Owner reply review).
- [ ] Action `RecalculateListingRatingAction.php` (Hitung ulang rating_avg dan review_count pada listing).
- [ ] Action `ToggleWishlistAction.php` (Toggle tambah/hapus favorit).
- [ ] Repositories: `ReviewRepositoryInterface.php`, `EloquentReviewRepository.php`, `WishlistRepositoryInterface.php`, `EloquentWishlistRepository.php`.
- [ ] Register bindings di `AppServiceProvider.php`.
- [ ] **Verification:** Pest Unit Tests (`CreateReviewActionTest.php`, `ToggleWishlistActionTest.php`).

### Task 3: HTTP Layer, FormRequests, Policies & Routes
- [ ] FormRequests: `StoreReviewRequest.php`, `ReplyReviewRequest.php`, `ToggleWishlistRequest.php`.
- [ ] Policies: `ReviewPolicy.php`, `WishlistPolicy.php`.
- [ ] Controllers: `Review\ReviewController.php`, `Wishlist\WishlistController.php`.
- [ ] Routes in `routes/web.php` (`/kos/{slug}/reviews`, `/reviews/{id}/reply`, `/wishlist`, `/wishlist/toggle`).
- [ ] **Verification:** Pest Feature Tests (`ReviewFlowTest.php`, `WishlistFlowTest.php`).

### Task 4: Frontend UI Components (React 19 + Taste Skill)
- [ ] TypeScript interfaces: `resources/js/types/review.ts` & `wishlist.ts`.
- [ ] Component `resources/js/features/reviews/components/ReviewList.tsx` (Star distribution breakdown, verified tenant badges, owner replies).
- [ ] Component `resources/js/features/reviews/components/ReviewForm.tsx` (Interactive 1-5 star selector + review form).
- [ ] Page `resources/js/Pages/Wishlist/Index.tsx` (Daftar kos favorit tersimpan).
- [ ] Update `resources/js/Pages/Listings/Show.tsx` (Sertakan ReviewList & ReviewForm).
- [ ] Update `resources/js/features/listings/components/ListingCard.tsx` (Hubungkan tombol wishlist ke `/wishlist/toggle`).
- [ ] **Verification:** `npx tsc --noEmit` (0 error), `npm run lint`, `npm run build`.

### Task 5: Full Verification Gate & Milestone 4 Completion
- [ ] `composer validate`
- [ ] `./vendor/bin/pint --test`
- [ ] `composer phpstan` (Level Max)
- [ ] `php artisan test` (Pest suite hijau)
- [ ] `npx tsc --noEmit`
- [ ] `npm run lint`
- [ ] `npm run build`
- [ ] `graphify update .`
- [ ] Update `tasks.md` & walkthrough.
