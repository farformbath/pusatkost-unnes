# 💻 Development Setup & Developer Workflow — UNNESKost v3

Dokumen ini memandu pengembang dalam menyiapkan lingkungan kerja lokal (*Local Development Environment*), standar *coding*, serta alur kerja harian.

---

## 1. Persyaratan Sistem

### Pilihan A: Lingkungan Docker (Direkomendasikan)
- **OS:** Windows 10/11 (dengan WSL2 & Docker Desktop) / Linux / macOS.
- **Docker Engine:** >= 24.x
- **Docker Compose:** >= 2.x

### Pilihan B: Lingkungan Lokal Native
- **PHP:** >= 8.4 (ekstensi: `pdo_pgsql`, `redis`, `gd`, `intl`, `mbstring`, `zip`)
- **Composer:** >= 2.8
- **Node.js:** >= 22 (LTS)
- **PostgreSQL:** 16.x
- **Redis:** 7.x
- **Meilisearch:** >= 1.x (opsional, fallback ke DB search jika tidak aktif)

---

## 2. Langkah Setup Lingkungan

### Menggunakan Docker Compose:
```bash
# 1. Salin konfigurasi environment
cp .env.example .env

# 2. Jalankan make setup
make setup

# Atau perintah manual docker compose:
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app npm install
docker compose exec app npm run build
```

### Menggunakan Lingkungan Lokal Native:
```bash
# 1. Install dependensi
composer install
npm install

# 2. Salin env & generate app key
cp .env.example .env
php artisan key:generate

# 3. Konfigurasi database di .env (PostgreSQL)
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=unneskost
# DB_USERNAME=postgres
# DB_PASSWORD=postgres

# 4. Migrasi dan seeder
php artisan migrate:fresh --seed

# 5. Jalankan server
php artisan serve
npm run dev
```

---

## 3. Perintah Kerja Harian (*Workflow Commands*)

| Tujuan | Perintah Make | Perintah Artisan / NPM |
|---|---|---|
| Menjalankan dev environment | `make up` | `php artisan serve` & `npm run dev` |
| Menghentikan environment | `make down` | `Ctrl + C` / stop process |
| Menjalankan Unit & Feature Test | `make test` | `php artisan test` & `npm run test` |
| Analisis Statis PHP (Max level) | `make analyse` | `./vendor/bin/phpstan analyse` |
| Standardisasi Format Kode PHP | `make lint` | `./vendor/bin/pint` |
| Type-check Frontend TS | `npm run type-check` | `npx tsc --noEmit` |
| Format & Lint Frontend | `npm run lint` | `npx eslint . --fix` & `npx prettier --write .` |
| Reset Database & Dummy Data | `make fresh` | `php artisan migrate:fresh --seed` |
| Generate Tipe TypeScript dari PHP | `make types` | `php artisan typescript:transform` |
| Akses Shell Container | `make shell` | `docker compose exec app bash` |

---

## 4. Konvensi Koding & Struktur File

### 1. Menambahkan Fitur Bisnis Baru (Contoh: Cancel Booking)
1. **DTO:** Buat `CancelBookingDTO` di `app/Domain/Booking/DTOs/`.
2. **Action:** Buat `CancelBookingAction` di `app/Domain/Booking/Actions/`.
3. **Event:** Buat `BookingCancelled` di `app/Domain/Booking/Events/`.
4. **FormRequest:** Buat `CancelBookingRequest` di `app/Http/Requests/Booking/`.
5. **Controller:** Tambahkan method tipis di `BookingController` yang memanggil `CancelBookingAction`.
6. **Frontend:** Buat komponen `CancelDialog.tsx` di `resources/js/features/booking/`.
7. **Pest Test:** Buat `tests/Feature/Booking/CancelBookingTest.php`.

### 2. Aturan Frontend React:
- Gunakan komponen dasar dari `resources/js/components/ui/`.
- Jangan menggunakan `any` pada tipe data TypeScript.
- Gunakan `useForm` dari `@inertiajs/react` atau `react-hook-form + zod` untuk form handling.
- Gunakan helper format uang `formatRupiah(amount)` dari `resources/js/lib/formatters.ts`.
