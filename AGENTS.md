# 🤖 AI Agent Guidelines & Architecture Rules — UNNESKost v3

Dokumen ini adalah aturan wajib bagi semua AI coding agent (Claude Code, Cursor, Windsurf, Antigravity, dll) dan developer yang bekerja pada codebase **UNNESKost v3**.

Acuan utama spesifikasi sistem adalah **[`unneskost-prompt-final.md`](file:///c:/projects/unneskost2/unneskost-prompt-final.md)**.

---

## 1. Peran & Prinsip Utama

- **Peran:** Principal Software Architect + Senior Fullstack Engineer + DevOps Engineer.
- **Arsitektur:** Modular Monolith (Laravel 13 + Inertia.js v3 + React 19 + TypeScript Strict + Tailwind CSS v4 + PostgreSQL 16 + Redis + Meilisearch).
- **Prinsip Utama:**
  1. **Modular Monolith, bukan Microservices.** Bounded context jelas di `app/Domain/`.
  2. **Simplicity over cleverness (Ponytail / YAGNI).** Hindari *over-engineering* dan abstraksi berlebih. Terapkan *The Necessity Ladder*: standar library & fitur native platform lebih utama daripada menambah kode kustom/dependency.
  3. **Container-first & 12-factor.** State disimpan di DB/Redis/S3, proses stateless.
  4. **Security by design** sejak commit pertama.
  5. **Testable by design.** Logika bisnis hidup di Domain layer (HTTP-agnostic).
  6. **Type Safety end-to-end.** PHP Types + DTO + spatie/typescript-transformer + React TS Strict.

---

## 2. 🚨 Larangan Keras (Non-Negotiable Rules)

1. ❌ **Dilarang menaruh logika bisnis di Controller atau Route closure.** Controller hanya bertindak sebagai adapter tipis: validasi via FormRequest → panggil 1 Action/Query → return Inertia response / API Resource.
2. ❌ **Dilarang menggunakan JavaScript tanpa tipe atau `any` di TypeScript.** Seluruh kode frontend harus TypeScript strict.
3. ❌ **Dilarang menggunakan Axios.** Gunakan router/useHttp bawaan Inertia.js v3.
4. ❌ **Dilarang query Eloquent panjang di route closure atau komponen React.** Selalu lewat Query Object, Repository, atau Action di Domain layer.
5. ❌ **Dilarang hardcode credential, token, secret, atau password.** Semua via `.env` dan `config/`. Sediakan `.env.example` yang lengkap.
6. ❌ **Dilarang destructive command** (drop database, force push, penghapusan file massal) tanpa konfirmasi pengguna.
7. ❌ **Dilarang menambah library/dependency tanpa alasan konkret dan persetujuan.**
8. ❌ **Dilarang menggunakan Float untuk nilai moneter.** Nilai mata uang (Rupiah) selalu disimpan dalam integer terkecil (satuan Rupiah bulat).
9. ❌ **Dilarang otorisasi `if ($user->role === 'admin')` di controller.** Selalu gunakan Laravel Policy / Gate dan Spatie Permissions.

---

## 3. Struktur Backend — DDD-Lite & Action Pattern

```
app/
├── Domain/                      # 🧠 INTI BISNIS (Bebas dari dependensi HTTP)
│   ├── Listing/                 # Kos, Kamar, Fasilitas, Foto
│   │   ├── Models/              # Rich models, scopes, relations, invariants
│   │   ├── Actions/             # 1 class = 1 operasi bisnis (CreateListingAction, dst.)
│   │   ├── Repositories/        # Interface & Eloquent Implementation
│   │   ├── Services/            # Logika lintas model (RoomAvailabilityService)
│   │   ├── DTOs/                # Data Transfer Objects (readonly)
│   │   ├── Enums/               # RoomStatus, ListingType, dst.
│   │   ├── Events/              # Domain Events (ListingCreated, dst.)
│   │   ├── Listeners/           # Event Listeners (queued notification, index sync)
│   │   └── Policies/            # Otorisasi domain
│   ├── Booking/                 # Pemesanan, Pembayaran, Status Audit
│   ├── Review/                  # Rating & Ulasan + Moderasi
│   ├── Favorite/                # Wishlist kos
│   ├── Search/                  # Scout query & Meilisearch sync
│   └── User/                    # User, Profil, Role & Permission
├── Http/                        # 🚪 ADAPTER (Tipis)
│   ├── Controllers/             # Admin/, Owner/, Public/, Booking/, Api/V1/
│   ├── Requests/                # FormRequest per operasi
│   ├── Middleware/              # Role check, Inertia share, Correlation ID
│   └── Resources/               # API Resources untuk REST /api/v1
├── Support/                     # Helper murni (ApiResponse, Money, Formatting)
└── Providers/                   # Service Providers & Repository Bindings
```

### Aturan Domain Layer:
- **Action Pattern:** Setiap Action hanya memiliki 1 public method (`handle` atau `__invoke`).
- **Domain Agnostic:** Tidak boleh memanggil `request()`, `session()`, `auth()`, atau `redirect()` di dalam `app/Domain/`. Semua data masuk via DTO atau method parameter.
- **Side-effects via Events:** Efek samping (kirim email, update indeks search, logging audit) dijalankan oleh Event Listener secara asynchronous/queued.

---

## 4. Struktur Frontend — Feature-Based & TypeScript Strict

```
resources/js/
├── types/                        # Generated types (typescript-transformer) & global.d.ts
├── lib/                          # Utility functions (cn, formatRupiah, date helpers)
├── hooks/                        # Custom React hooks (useDebounce, useToast, dll.)
├── components/
│   ├── ui/                       # Design system Radix/shadcn style (Button, Modal, Input, dst.)
│   └── shared/                   # Komponen lintas fitur (ListingCard, FilterBar, RoomBadge, StickyBooking)
├── layouts/                      # PublicLayout, AuthenticatedLayout, OwnerLayout, AdminLayout
└── features/                     # Modul berbasis fitur
    ├── listings/                 # Pages (Index, Show), search components
    ├── booking/                  # BookingWidget, BookingSummary, CancelDialog
    ├── review/                   # ReviewForm, ReviewList
    ├── favorites/                # FavoriteButton, WishlistGrid
    ├── owner/                    # Owner Dashboard & manajemen kos/kamar
    ├── admin/                    # Admin Dashboard, user management, moderasi
    └── profile/                  # Pengaturan akun
```

---

## 5. Standar Testing & Code Quality

Setiap fitur yang diselesaikan harus memenuhi **Definition of Done (DoD)**:
1. `tsc --noEmit` bersih tanpa error TypeScript.
2. `phpstan analyse` level max bersih tanpa error.
3. `vendor/bin/pint --test` format kode rapi sesuai standar Laravel.
4. `npm run lint` & `npm run format` bersih.
5. Unit test & Feature test (Pest) hijau untuk semua alur bisnis inti.
6. Responsive di mobile dan desktop dengan state lengkap (hover, active, disabled, loading, empty, error).

---

## 6. Milestone Eksekusi

- **M0 Fondasi:** Scaffold Laravel 13 + React 19 TS, Docker Compose, CI/CD Actions, PHPStan max, Linting.
- **M1 Listing & Kamar:** CRUD Listing, Room, Facilities, Image Upload, Rich Model, Public view & Owner management.
- **M2 Booking & Payment:** Booking flow, validasi ketersediaan (DB lock), audit status kamar, payment status, queued events.
- **M3 Search & Filter:** Laravel Scout + Meilisearch + DB fallback query.
- **M4 Review & Wishlist:** Rating pasca-booking, moderasi admin, favorites.
- **M5 Auth & RBAC:** Sanctum, Spatie Roles (Admin, Owner, User), email verification, in-app notifications.
- **M6 Polish & E2E:** Error boundaries, empty states, Playwright E2E test, dokumentasi final.
