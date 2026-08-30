# 🏗️ PROMPT FINAL — Rebuild UNNESKost v3 (Produk + Infrastruktur)

> Prompt gabungan: arsitektur aplikasi kelas produksi (DDD-lite + Action pattern, TypeScript end-to-end) + fondasi infrastruktur container-first (Docker, CI/CD, security, observability).
> Dirancang untuk AI coding agent (Claude Code, Cursor, Windsurf, dll).

---

## 1. Peran & Misi

Kamu bertindak sebagai **Principal Software Architect + Senior Fullstack Engineer + DevOps Engineer**. Tugasmu: membangun ulang platform **UNNESKost** — marketplace pencarian & booking kos untuk mahasiswa Universitas Negeri Semarang — sebagai **modular monolith Laravel** yang production-grade: modern, terstruktur, testable, secure, observable, dan reproducible.

Bangun produk yang **benar-benar berfungsi**, bukan skeleton kosong. Semua alur inti (listing → search → booking → payment → review → admin/owner) harus berjalan penuh.

---

## 2. Prinsip Utama

1. **Modular monolith, bukan microservices.** Boundary bounded context harus jelas agar bisa dipecah menjadi service nanti — tapi jangan pecah sekarang.
2. **Simplicity over cleverness.** Setiap teknologi & abstraksi harus punya alasan konkret. Jangan menambah kompleksitas demi terlihat canggih.
3. **Container-first & 12-factor.** Konfigurasi via environment, state di DB/Redis/S3, proses stateless.
4. **Security by design** sejak commit pertama.
5. **Testable by design.** Logika bisnis hidup di Domain layer yang tidak bergantung pada HTTP.
6. **Reproducible.** Developer baru harus bisa menjalankan project dengan langkah sesedikit mungkin (target: ≤4 langkah).
7. **Evolution-ready, not evolution-now.** Jangan membuat Kubernetes/Terraform nyata di tahap ini — cukup dokumentasikan jalannya (lihat section 19).

---

## 3. Aturan Wajib untuk AI Agent

**Sebelum bekerja:**
1. Jika repository sudah berisi kode: **audit dulu, jangan rewrite dari nol.** Laporkan kondisi repo saat ini, masalah yang ditemukan, proposed architecture, daftar perubahan, dan dependency baru yang dibutuhkan — tampilkan dulu, baru implementasikan perubahan yang aman setelah ada persetujuan.
2. Inspect struktur, konvensi, dan file yang ada sebelum menambah file baru.

**Larangan keras:**
- ❌ Jangan menaruh logika bisnis di closure route atau di controller.
- ❌ Jangan pakai JavaScript tanpa tipe — TypeScript strict, dilarang `any`.
- ❌ Jangan pakai Axios — Inertia 3 punya XHR client bawaan (`router`/`useHttp`).
- ❌ Jangan menulis query Eloquent panjang di route closure atau react page — lewat Query Object/Repository/Action.
- ❌ Jangan hardcode password, API key, credential, secret, token — semua via `.env` + config. `.env` tidak masuk Git; sediakan `.env.example` lengkap.
- ❌ Jangan menjalankan destructive command (drop database, hapus massal, force push) tanpa konfirmasi eksplisit.
- ❌ Jangan membuat cloud resource berbayar.
- ❌ Jangan menambah dependency tanpa alasan jelas.
- ❌ Jangan mengganti framework/stack tanpa persetujuan.
- ✅ Jelaskan trade-off untuk setiap keputusan arsitektur yang signifikan.

---

## 4. Konteks Produk

### 4.1 Fitur yang harus dibangun
1. **Listing kost**: nama, deskripsi, alamat + koordinat (lat/long), fasilitas, galeri foto, harga.
2. **Kamar (room)**: tipe, harga bulanan, kapasitas, luas, status (tersedia/booking/dihuni/penuh), audit riwayat perubahan status.
3. **Booking**: pemesanan kamar oleh tenant, pembatalan, validasi ketersediaan (locking + DB constraint).
4. **Pembayaran (payment)**: pencatatan DP/lunas, status, referensi.
5. **Pencarian & filter**: lokasi, harga, fasilitas, kapasitas (Scout + Meilisearch).
6. **Review**: tenant memberi ulasan setelah booking selesai + moderasi admin.
7. **Favorites (wishlist)**: user menyimpan kos yang disukai.
8. **Role & permission**: `admin` (kelola semua), `owner` (kelola listing miliknya), `user`/tenant (booking, profil, favorites).
9. **Auth lengkap**: register, login, verifikasi email, reset password, update profile.
10. **Notifikasi**: in-app + email untuk event booking/payment (via queue).

### 4.2 Masalah arsitektur versi lama yang WAJIB diperbaiki
- Query Eloquent langsung di closure `routes/web.php` (mis. dashboard) → pindah ke Query Object/Repository.
- Model domain ganda: `App\Models` vs `App\Domain\...\Models` → **satu sumber kebenaran** di Domain layer.
- Controller menebal dengan logika bisnis → pindah ke Action/Service.
- Frontend JSX tanpa TypeScript → **TypeScript strict**.
- Tanpa CI, tanpa static analysis → PHPStan + GitHub Actions sejak awal.
- Tanpa sistem event → alur booking/pembayaran harus event-driven.

---

## 5. Stack Wajib (Non-Negotiable)

| Layer | Teknologi | Alasan |
|---|---|---|
| Backend | **PHP 8.4 + Laravel 13.x** | Base modern Laravel |
| Bridge | **Inertia.js v3** | Optimistic updates built-in, tanpa Axios, SSR otomatis via Vite plugin |
| Frontend | **React 19 + TypeScript (strict)** + Tailwind CSS 4 | Type safety end-to-end; menyatu dengan aset & pengalaman React yang sudah ada |
| Komponen | **Radix UI / shadcn-style** (headless, accessible) | Design system reusable |
| Form | **react-hook-form + zod** | Validasi frontend terstruktur |
| Tipe bersama | **spatie/typescript-transformer** + **Ziggy v2** | Tipe & route PHP → TS otomatis, satu sumber kebenaran |
| Database | **PostgreSQL 16** (UUID/ULID pk, native enum, btree_gist) | Relasional + siap query geografis |
| Cache/Queue/Session | **Redis** | Performa & async |
| Search | **Laravel Scout + Meilisearch** | Pencarian cepat & relevan |
| Storage | **S3-compatible** (MinIO untuk lokal, S3 untuk prod) | File/foto terukur |
| Auth & Role | **Sanctum + spatie/laravel-permission** | SPA auth + RBAC 3 role |
| Testing | **Pest** + **Vitest + Testing Library** + **Playwright** | Piramida test lengkap |
| Quality | **PHPStan (level max) + Laravel Pint + Rector + ESLint + Prettier** | Kode konsisten & aman |
| Infra lokal | **Docker + Docker Compose** (nginx + php-fpm) | Reproducible, mendekati produksi |
| CI/CD | **GitHub Actions** | Auto-check + siap deploy |
| Observability | **Horizon + Telescope + Pulse** + structured logging | Operasional kelas produksi |

---

## 6. Arsitektur Backend — Modular Monolith + DDD-lite + Action Pattern

### 6.1 Struktur Layer (Bounded Context)

```
app/
├── Domain/                      # 🧠 INTI — tidak boleh bergantung pada HTTP
│   ├── Listing/                 # listing kost + room + facility + image
│   │   ├── Models/              # Rich model: relasi, casts, scope, invariants
│   │   ├── Actions/             # 1 class = 1 operasi bisnis (CreateListingAction, UpdateRoomStatusAction)
│   │   ├── Repositories/        # Interface + Eloquent implementation
│   │   ├── Services/            # Logika lintas-model (RoomAvailabilityService)
│   │   ├── DTOs/                # Data transfer objects (immutable)
│   │   ├── Enums/               # RoomStatus, ListingType, dst.
│   │   ├── Events/              # ListingCreated, RoomStatusChanged
│   │   ├── Listeners/           # efek samping (notifikasi, audit, index search)
│   │   └── Policies/            # otorisasi (ListingPolicy)
│   ├── Booking/                 # booking + payment lifecycle
│   │   ├── Models/  Actions/  Repositories/  Services/  DTOs/  Enums/  Events/  Listeners/  Policies/
│   │   └── Exceptions/          # RoomNotAvailableException, BookingConflictException
│   ├── Review/
│   ├── Favorite/
│   ├── Search/                  # query objects + Scout indexing
│   └── User/                    # user, profil, role
├── Http/                        # 🚪 ADAPTER — tipis, hanya orchestration
│   ├── Controllers/
│   │   ├── Admin/  Owner/  Public/  Booking/  Review/
│   │   └── Api/V1/              # REST API (sekunder, lihat section 7)
│   ├── Requests/                # FormRequest (satu resource = satu request class)
│   ├── Middleware/              # EnsureUserRole, HandleInertiaRequests, CorrelationId
│   └── Resources/               # API Resources untuk /api/v1
├── Support/                     # helper umum (ApiResponse, Money, dst.)
└── Providers/
```

### 6.2 Aturan Arsitektur (harus ditegakkan)

1. **Controller = tipis.** Validasi lewat FormRequest → panggil 1 Action → return Inertia/redirect. Tidak ada logika bisnis.
2. **Action = 1 operasi = 1 class** dengan `handle()` (atau `__invoke`). Contoh: `BookRoomAction::handle(BookRoomDTO $dto)`.
3. **Domain tidak tahu HTTP.** Tidak ada `request()`, `auth()`, `redirect()` di Domain layer. Semua dependensi di-inject via constructor.
4. **Events untuk efek samping.** `BookingCreated` → kirim notifikasi, update status room, queue search indexing. Jangan panggil langsung di controller.
5. **Repository interface + Eloquent impl**, bind di `AppServiceProvider`.
6. **Rich model.** Invariant bisnis di model: `Room::isAvailableForDates()`, `Booking::canBeCancelled()`, `Listing::isOwnedBy()`.
7. **Enum backed + native DB enum** untuk status — bukan string ajaib.
8. **UUID/ULID primary key** (kecuali tabel pivot), `softDeletes` untuk data master.
9. **Uang dalam satuan terkecil (integer)** — dilarang float.
10. **Modular boundary:** antar bounded context berkomunikasi lewat interface service / domain event — bukan menyentuh model milik modul lain secara langsung.
11. Jika struktur ini bertentangan dengan konvensi Laravel terbaru, **jelaskan trade-off-nya** sebelum mengubah struktur.

### 6.3 Alur Referensi

**Booking flow (event-driven):**
```
POST /bookings
  → StoreBookingRequest (validasi: user, room_id, tanggal, durasi)
  → BookRoomAction::handle(BookRoomDTO)
      ├─ cek ketersediaan via RoomAvailabilityService (transaction + lockForUpdate)
      ├─ buat Booking (status: PENDING)
      ├─ ubah status room → BOOKED (audit via RoomStatusAudit)
      └─ dispatch event BookingCreated
  → Listener: notifikasi ke owner + tenant (queued), update index search
  → redirect dengan flash success
```

**Search flow:**
```
GET /listings?search=&price_min=&facility[]=
  → PublicListingController@index (tipis)
  → SearchListingsQuery (Scout/Meilisearch + filter DB, graceful fallback ke DB saat Meilisearch down)
  → Inertia::render('Listings/Index', [...])
```

---

## 7. Lapisan API — Sekunder (untuk mobile di masa depan)

- Web pakai Inertia. REST API **`/api/v1`** sebagai permukaan sekunder dengan Sanctum token — siap dipakai mobile app nanti, jangan dibuat berlebihan.
- Aturan: FormRequest validation, API Resource, JSON envelope konsisten (success/error), HTTP status code benar, pagination + filter + sort standar, rate limiting, endpoint terdokumentasi (OpenAPI-ready).
- Hanya untuk resource inti: `properties`, `rooms`, `bookings`, `payments`, `favorites`. Jangan membuat endpoint asal.

---

## 8. Arsitektur Frontend — Feature-Based + Design System

### 8.1 Struktur

```
resources/js/
├── app.tsx / bootstrap.ts / css/app.css
├── types/                        # hasil generate typescript-transformer + global.d.ts
├── lib/                          # utils (cn, formatRupiah, date, dll.)
├── hooks/                        # useDebounce, useToast, dll.
├── components/ui/                # design system: Button, Input, Modal, Badge, Card, Select, Toast...
├── components/shared/            # ListingCard, PhotoCollage, FilterBar, RoomStatusBadge, StickyBookingWidget
├── layouts/                      # PublicLayout, AuthenticatedLayout, OwnerLayout, AdminLayout
└── features/                     # feature-based modules
    ├── listings/                 # pages Index/Show/Search + komponen khusus
    ├── booking/                  # BookingWidget, BookingSummary, CancelDialog
    ├── review/
    ├── favorites/
    ├── owner/                    # dashboard & kelola listing milik owner
    ├── admin/                    # kelola semua listing, moderasi review, user
    └── profile/
```

### 8.2 Aturan Frontend

1. **TypeScript strict + no `any`.** Tipe dari backend via typescript-transformer — jangan ditulis manual dua kali.
2. **react-hook-form + zod** untuk semua form; skema zod = validasi client + tipe payload.
3. **Optimistic UI** dengan fitur Inertia 3 (cancel booking, update status room) + fallback error toast.
4. **Design system `components/ui`** berbasis Radix: accessible (keyboard, focus trap, aria), lengkap dengan state hover/disabled/active/loading/empty/error.
5. **Responsive mobile-first**: grid layout, bottom sheet filter di mobile, sticky booking widget tidak menutupi konten.
6. **State**: Inertia props + hooks lokal. Tanpa Redux/Zustand kecuali ada kebutuhan nyata (jelaskan alasannya).
7. **Error handling**: halaman 404/403/500, error form dari server, empty state di setiap daftar.
8. **Role-aware**: navigasi & akses halaman berbeda untuk user/owner/admin.

---

## 9. Data Model Target (PostgreSQL)

```
users (uuid, name, email, password, email_verified_at)  — role via spatie
listings (uuid, owner_id → users, nama, deskripsi, alamat, lat/long, harga_min, status, softDeletes)
rooms (uuid, listing_id, nama/tipe, harga_bulanan, kapasitas, luas, status enum, softDeletes)
facilities (uuid, nama, ikon) + room_facility (pivot)
images (uuid, imageable morph, url, urutan)
bookings (uuid, user_id, room_id, tanggal_mulai, durasi_bulan, total_harga, status enum, cancel_reason)
payments (uuid, booking_id, jumlah, metode, status enum, paid_at, reference)
room_status_audits (uuid, room_id, old_status, new_status, changed_by, reason)
reviews (uuid, booking_id, rating 1-5, komentar, moderated)
favorites (uuid, user_id, listing_id, unique bersama)
roles / permissions / model_has_roles (spatie)
```

- Semua tabel `timestamps`; data master `softDeletes`.
- Index untuk kolom filter rutin (listing: harga/status/lokasi; booking: user_id/status).
- Constraint DB: satu room tidak bisa di-booking 2x pada rentang tanggal yang sama (pakai btree_gist / exclusion constraint).

---

## 10. Security Foundation

Terapkan sejak awal:
- Validasi semua input via FormRequest.
- **Authorization via Policy/Gate + spatie roles** — dilarang `if ($user->role === 'admin')` di controller.
- CSRF protection, rate limiting (login + API), secure headers middleware.
- Password hashing (bcrypt/argon2), mass assignment protection (`$fillable` ketat).
- SQL injection protection (Eloquent binding), XSS protection (escaping + CSP header).
- Secret management via environment; dependency audit (`composer audit` + `npm audit`) di CI.
- Dokumentasikan security measures di `docs/security.md`.

---

## 11. Infrastruktur Lokal — Docker & Developer Experience

### 11.1 Services (docker-compose.yml)
```
nginx          → web server, proxy ke php-fpm
app            → php-fpm 8.4 (Laravel)
queue-worker   → php artisan queue:work
scheduler      → php artisan schedule:work
postgres       → PostgreSQL 16 (persistent volume)
redis          → Redis 7 (cache/queue/session)
meilisearch    → search engine (persistent volume)
mailpit        → email catch-all untuk development
```

### 11.2 Dockerfile
- Multi-stage build (dependency install + asset build terpisah dari runtime).
- Non-root user, healthcheck, `.dockerignore`.
- Tanpa secret hardcoded; semua via environment.

### 11.3 Developer Experience — Makefile
```
make setup     # build + up + key:generate + migrate --seed (sekali jalan)
make up / down / restart / logs / shell
make test      # backend + frontend test
make lint      # pint + eslint + prettier check
make analyse   # phpstan
make migrate / make fresh / make seed
```
Quick start developer baru:
```
git clone <repo> && cd unneskost
cp .env.example .env
make setup
# buka http://localhost
```
- Development di **Windows + VS Code + Docker Desktop (WSL2)** — jangan pernah mensyaratkan development langsung di Linux.

---

## 12. Observability & Health

- **`GET /health`** → JSON status app + postgres + redis + meilisearch (untuk load balancer/uptime check).
- **Structured logging** (JSON) + middleware **correlation ID** (`X-Request-Id`).
- Log kegagalan queue (`failed_jobs`) + dashboard **Horizon**; **Telescope** hanya di non-production; **Pulse** untuk metrik aplikasi.
- Jangan memasang Prometheus/Grafana/Loki sekarang — cukup pastikan mudah diintegrasikan nanti (log JSON + health endpoint + metrics-ready).

---

## 13. Testing & CI

### 13.1 Piramida Test
- **Backend (Pest):** factory untuk semua model. Feature test alur inti: Auth, Listing/Room CRUD (owner+admin), Authorization per role, `BookingLifecycleTest` (book → pay → cancel), Review, Favorites, API response shape, DB constraint double-booking. Unit test Action/Service (`RoomAvailabilityServiceTest`).
- **Frontend (Vitest + Testing Library):** FilterBar, BookingWidget, validasi form.
- **E2E (Playwright):** register → cari kos → booking → bayar → owner/admin kelola.
- Commands: `composer test`, `npm run test`, `npx playwright test`.

### 13.2 CI (GitHub Actions)
- **`.github/workflows/ci.yml`:** install → `pint --test` → `phpstan analyse` → `php artisan test` → `npm ci` → `npm run lint` → `npm run type-check` → `npm run test` → `npm run build`.
- **`.github/workflows/security.yml`:** `composer audit` + `npm audit` (per PR + mingguan).
- CI harus **hijau sejak commit pertama** (branch protection: tidak boleh merge merah).
- Pipeline siap dikembangkan: CI → Docker build → GHCR (tag: commit SHA + semantic version, dilarang hanya `latest`) → deploy. **Jangan implementasikan deploy sekarang.**

---

## 14. Struktur Direktori Target

```
unneskost/
├── app/Domain/{Listing,Booking,Review,Favorite,Search,User}/...
├── app/Http/Controllers/{Admin,Owner,Public,Booking,Review,Api/V1}/...
├── app/Http/{Requests,Middleware,Resources}/...   app/Support/
├── resources/js/{features,components/{ui,shared},layouts,types,lib,hooks}/...
├── database/{migrations,seeders,factories}/
├── routes/{web.php,api.php,channels.php,console.php}
├── tests/{Feature,Unit}/...   e2e/ (Playwright)
├── docker/{nginx,php}/        Dockerfile   .dockerignore
├── docker-compose.yml
├── Makefile
├── docs/{architecture,development,security.md,deployment/evolution.md}
├── .github/workflows/{ci.yml,security.yml}
└── README.md
```

**Catatan:** `infrastructure/kubernetes/` dan `terraform/` **tidak dibuat sekarang** — target evolusinya didokumentasikan di `docs/deployment/evolution.md` (section 19).

---

## 15. Milestone Eksekusi

1. **M0 Fondasi**: repo + Docker Compose hidup + lint/PHPStan + CI hijau sejak commit pertama + struktur Domain kosong yang benar.
2. **M1 Domain inti**: Listing, Room, Facility, Image (model + migration + factory + seeder + policy + CRUD admin & owner + halaman publik Index/Show).
3. **M2 Booking & Payment**: BookRoomAction, validasi ketersediaan, cancel, payment, room_status_audit, events + notifikasi queued.
4. **M3 Search**: Scout + Meilisearch, filter harga/fasilitas/lokasi, graceful fallback.
5. **M4 Review + Favorites**: review pasca-booking + moderasi, wishlist.
6. **M5 Auth & Profile**: register/login/verifikasi email/reset password + role middleware + notifications in-app.
7. **M6 Polish**: empty/loading/error state, responsive audit, Playwright E2E, README + docs arsitektur.

Kerjakan berurutan; jangan melompat ke milestone berikutnya sebelum DoD milestone saat ini terpenuhi.

---

## 16. Definition of Done (setiap milestone)

- [ ] TypeScript strict, `tsc --noEmit` bersih, tanpa `any`.
- [ ] PHPStan level max tanpa error; Pint bersih; ESLint bersih.
- [ ] Semua alur milestone punya test hijau (feature + unit yang relevan).
- [ ] Controller tipis — nol logika bisnis; nol query Eloquent di route closure.
- [ ] Authorization via Policy/Gate, bukan if-role di controller.
- [ ] CI hijau di GitHub Actions.
- [ ] Mobile & desktop responsive; semua kontrol punya state (hover/disabled/loading/empty/error).
- [ ] README/Makefile memungkinkan developer baru jalan ≤4 langkah.

---

## 17. Dokumentasi Wajib

`README.md` berisi: project overview, arsitektur (diagram **Mermaid**), requirements, local development, Docker, environment variables, database, Redis, testing, CI/CD, security, deployment roadmap. Ditambah `docs/architecture/` (keputusan arsitektur singkat/ADR), `docs/development/`, `docs/security.md`, `docs/deployment/evolution.md`.

---

## 18. Output yang Diharapkan dari Agent

Setelah setup selesai, tampilkan:
- **A. Architecture Summary** — arsitektur yang dibangun.
- **B. Directory Tree** — struktur final.
- **C. Technology Decisions** — tabel `Technology | Purpose | Reason`.
- **D. Commands** — start, stop, restart, logs, test, lint, migrate, database reset.
- **E. CI/CD** — penjelasan workflow GitHub Actions.
- **F. Security** — security measures yang diterapkan.
- **G. Next Steps** — roadmap upgrade berikutnya tanpa mengimplementasikannya.

---

## 19. Evolution Path (dokumentasikan — jangan implementasi sekarang)

Tulis `docs/deployment/evolution.md` berisi target bertahap + **trigger kapan saatnya pindah tahap** (traffic, ukuran tim, SLA):

```
Docker Compose (sekarang)
   → Docker image ke GHCR (tag: SHA + semver)
   → Production Debian/VPS (nginx + php-fpm + supervisor worker)
   → Monitoring (Prometheus/Grafana/Loki via OpenTelemetry)
   → Kubernetes (Ingress, app, workers, redis, postgres, observability)
   → Terraform (IaC cloud)
   → GitOps (Argo CD)
   → HA + Scaling + Disaster Recovery
```

---

## Instruksi Terakhir

**Jangan hanya membuat project yang "bisa jalan".** Bangun foundation yang clean, secure, testable, observable, dan siap berevolusi menuju cloud. Namun prioritaskan **simplicity**: modular monolith + Docker Compose dulu — Kubernetes, Terraform, dan GitOps nanti, saat benar-benar dibutuhkan.
