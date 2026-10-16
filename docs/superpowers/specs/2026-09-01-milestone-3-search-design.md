# 📐 Design Specification: Milestone 3 — Modul Pencarian & Filter Cerdas

- **Tanggal:** 2026-09-01
- **Bounded Context:** `App\Domain\Search\`
- **Status:** In Progress
- **Referensi Kebutuhan:** [`requirements.md`](file:///c:/projects/unneskost2/requirements.md) (FR-SEARCH-001 s.d. FR-SEARCH-008).

---

## 1. Arsitektur Domain & Boundary

Bounded Context `App\Domain\Search\` menangani pencarian teks bebas (*full-text / smart fuzzy search*), faceted filtering multi-kriteria, pengurutan cerdas (*sorting*), dan mekanisme *database fallback* ketika engine pencarian eksternal offline.

```
app/Domain/Search/
├── Actions/
│   └── SearchListingsAction.php       # Core search orchestration (Faceted Filter + Database Fallback)
├── DTOs/
│   └── SearchCriteria.php            # Strongly-typed search parameters
└── Repositories/
    ├── SearchRepositoryInterface.php
    └── EloquentSearchRepository.php
```

---

## 2. Kriteria Pencarian & Filter (*Faceted Filtering*)

### A. Parameter Pencarian (`SearchCriteria`)
1. `keyword` (?string): Pencarian teks pada nama kos, deskripsi, alamat, dan area.
2. `gender` (?GenderType): Filter tipe kos (`putri`, `putra`, `campur`).
3. `area` (?string): Filter wilayah UNNES (`Sekaran`, `Banaran`, `Taman Siswa`, `Gunungpati`, `Patemon`, `Menoreh`).
4. `minPrice` (?int): Batas minimal harga sewa bulanan dalam integer Rupiah.
5. `maxPrice` (?int): Batas maksimal harga sewa bulanan dalam integer Rupiah.
6. `facilityIds` (array<string>): Array UUID fasilitas (wajib memiliki seluruh fasilitas yang dicentang / *AND* logic atau *OR* logic).
7. `sort` (string):
   - `recommended` (default: rating tertinggi + ketersediaan kamar)
   - `price_asc` (harga termurah ke termahal)
   - `price_desc` (harga termahal ke termurah)
   - `rating_desc` (rating bintang tertinggi)
   - `newest` (terbaru ditambahkan)
8. `page` (int) & `perPage` (int): Paginasi.

---

## 3. Strategi Query & Database Fallback

Pencarian dijalankan melalui `EloquentSearchRepository` dengan query terindeks dan eager-loading:
1. Scope status: `Listing::query()->published()`
2. Filter keyword: `where(name ILIKE %keyword% OR address ILIKE %keyword% OR description ILIKE %keyword%)`
3. Filter gender: `where('gender_type', $criteria->gender)`
4. Filter area: `where('area', $criteria->area)`
5. Filter rentang harga:
   ```php
   $query->whereHas('rooms', function ($q) use ($criteria) {
       $q->where('status', RoomStatus::AVAILABLE)->where('available_rooms', '>', 0);
       if ($criteria->minPrice) $q->where('price_monthly', '>=', $criteria->minPrice);
       if ($criteria->maxPrice) $q->where('price_monthly', '<=', $criteria->maxPrice);
   });
   ```
6. Filter fasilitas:
   ```php
   foreach ($criteria->facilityIds as $facilityId) {
       $query->whereHas('facilities', function ($q) use ($facilityId) {
           $q->where('facilities.id', $facilityId);
       });
   }
   ```
7. Pengurutan teroptimasi untuk mencegah N+1 query.

---

## 4. Antarmuka Pengguna (Frontend React 19 + Taste Skill)

1. **`AirbnbSearchBar` (Enhanced)**: Dukungan kata kunci pencarian, pemilih area UNNES, dan tipe kos.
2. **`FilterDrawer.tsx` / `FilterSidebar.tsx`**: Drawer filter interaktif (mobile bottom sheet & desktop modal/sidebar) berisi:
   - Slider / input rentang budget (Rp 300.000 s.d. Rp 3.000.000).
   - Checkbox daftar fasilitas (WiFi Cepat, AC, Kamar Mandi Dalam, Kasur, Dapur Bersama, dll).
   - Pengurutan (Termurah, Termahal, Rating Tertinggi, Terbaru).
3. **Pencarian Instan Debounced**: Navigasi partial reload tanpa reload seluruh halaman (`preserveState: true`).
4. **Empty State & Saran Pencarian**: Ilustrasi bersih dan saran reset filter jika tidak ada hasil.
