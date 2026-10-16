# Milestone 3: Modul Pencarian & Filter Cerdas Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development or superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Membangun subsistem pencarian cerdas (*smart faceted search*), penyaringan multi-kriteria (wilayah, budget harga, tipe kamar, fasilitas), pengurutan, dan database fallback performan pada Bounded Context `App\Domain\Search\`.

**Architecture:** Modular Monolith DDD-lite, Action Pattern, DTO, Repository Pattern, Inertia.js v3, React 19 Strict, Tailwind CSS v4, Pest v3, PHPStan Level Max.

**Spec:** [`docs/superpowers/specs/2026-09-01-milestone-3-search-design.md`](file:///c:/projects/unneskost2/docs/superpowers/specs/2026-09-01-milestone-3-search-design.md)

---

## Global Invariants
- `declare(strict_types=1);` pada seluruh berkas PHP baru.
- Rentang harga (`min_price`, `max_price`) disimpan dan diproses dalam integer Rupiah murni (`BIGINT`/`int`).
- Strict Zero Axios (Inertia native routing & form).
- Strict TypeScript (`"strict": true`, 0 `any`).

---

## Task Breakdown

### Task 1: Domain DTOs, Repository & Search Action (`App\Domain\Search\`)
- [ ] Buat DTO: `SearchCriteria.php` (keyword, gender, area, minPrice, maxPrice, facilityIds, sort, page, perPage).
- [ ] Buat Repository: `SearchRepositoryInterface.php` & `EloquentSearchRepository.php`.
- [ ] Buat Action: `SearchListingsAction.php`.
- [ ] Daftarkan repository binding di `AppServiceProvider.php`.
- [ ] **Verification:** Pest Unit Tests (`SearchListingsActionTest.php`, `EloquentSearchRepositoryTest.php`).

### Task 2: HTTP Layer & FormRequests
- [ ] Buat FormRequest: `SearchListingRequest.php`.
- [ ] Update `Public\ListingController.php` untuk memanggil `SearchListingsAction` pada rute `GET /kos` dan `GET /`.
- [ ] Buat API Endpoint: `GET /api/v1/search/listings` (optional REST JSON format).
- [ ] **Verification:** Pest Feature Tests (`ListingSearchFilterTest.php`).

### Task 3: Frontend UI Components (React 19 + Taste Skill)
- [ ] Buat Filter Modal/Drawer: `resources/js/features/search/components/FilterModal.tsx`.
- [ ] Update `resources/js/features/listings/components/AirbnbSearchBar.tsx` (Dukungan kata kunci teks bebas).
- [ ] Update `resources/js/features/listings/components/FilterBar.tsx` (Tombol buka Filter Modal + indikator filter aktif + pengurutan).
- [ ] Update `resources/js/Pages/Listings/Index.tsx` untuk menampilkan hasil pencarian faceted secara responsif.
- [ ] **Verification:** `npx tsc --noEmit` (0 error), `npm run lint`, `npm run build`.

### Task 4: Full Verification Gate & Milestone 3 Completion
- [ ] `composer validate`
- [ ] `./vendor/bin/pint --test`
- [ ] `composer phpstan` (Level Max)
- [ ] `php artisan test` (Full Pest suite hijau)
- [ ] `npx tsc --noEmit`
- [ ] `npm run lint`
- [ ] `npm run build`
- [ ] `graphify update .`
- [ ] Update `tasks.md` & walkthrough.
