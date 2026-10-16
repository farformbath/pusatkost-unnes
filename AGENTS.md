# 🤖 AI Agent Guidelines & Architecture Rules — UNNESKost v3

Dokumen ini adalah aturan wajib bagi semua AI coding agent (Claude Code, Cursor, Windsurf, Antigravity, dll) dan developer yang bekerja pada codebase **UNNESKost v3**.

Acuan utama spesifikasi sistem adalah **[`unneskost-prompt-final.md`](file:///c:/projects/unneskost2/unneskost-prompt-final.md)** dan **[`requirements.md`](file:///c:/projects/unneskost2/requirements.md)**.

---

## 1. 🧰 Installed Agent Skills & Pembagian Tanggung Jawab

Proyek ini dilengkapi dengan 3 skill inti:

### ⚡ 1. Superpowers
Gunakan Superpowers sebagai **alur kerja rekayasa perangkat lunak utama (*Primary Engineering Workflow*)**:
- Perancangan rencana (*Planning*) & pemecahan tugas (*Task decomposition*).
- Siklus implementasi TDD (*Test-Driven Development*) langkah demi langkah.
- Verifikasi nyata berbasis perintah di setiap akhir task (*Never claim done without evidence*).
- Debugging sistematis & alur code review mandiri.

### 🐎 2. Ponytail
Gunakan Ponytail sebagai **disiplin arsitektur & anti-bloat (*Anti-Bloat & YAGNI Governance*)**:
- Evaluasi penambahan dependensi baru (utamakan *Standard Library* & fitur bawaan framework).
- Mencegah abstraksi spekulatif, folder kosong, interface/service/repository yang belum dibutuhkan.
- Menolak microservices, over-engineering, dan kode boilerplate yang tidak perlu.
- Prinsip **YAGNI** (*You Aren't Gonna Need It*) wajib dipatuhi.

### 🎨 3. Taste Skill
Gunakan Taste Skill **HANYA untuk implementasi antarmuka pengguna (UI/UX Frontend)**:
- Tata letak (*layout*), tipografi terukur, hierarki visual, spasi proporsional.
- Perilaku responsif (mobile-first), aksesibilitas, konsistensi warna brand Pusat Kost UNNES.
- **Dilarang untuk UI generic AI-slop**: hindari gradasi berlebihan, glassmorphism tak perlu, kartu melengkung ekstrem, dan animasi tanpa fungsi.
- **TIDAK BERLAKU** untuk backend, docker, database, CI/CD, dan static analysis.

---

## 2. 🏛️ Prinsip Rekayasa & Arsitektur

- **Arsitektur:** **Modular Monolith** (Laravel 12/13 + Inertia.js v3 + React 19 + TypeScript Strict + Tailwind CSS v4 + PostgreSQL 16 + Redis 7 + Meilisearch).
- **Dilarang keras Microservices.** Bounded context hidup di `app/Domain/`.
- **Backend:** PHP 8.4+, Eloquent ORM, PostgreSQL 16, Redis 7, Meilisearch.
- **Frontend:** React 19, Inertia.js v3, TypeScript Strict, Tailwind CSS v4.
- **Strict Zero Axios:** Gunakan router dan form helper bawaan Inertia.js v3.
- **Strict Zero Float:** Seluruh nilai moneter (Rupiah) disimpan dalam integer terkecil (satuan Rupiah bulat) pada tipe data `BIGINT`.
- **Strict PHP:** Setiap file PHP baru wajib diawali `declare(strict_types=1);`.
- **Strict TypeScript:** `tsconfig.json` wajib mengaktifkan `"strict": true` dan `"noImplicitAny": true` (0 `any`).

---

## 3. 🚨 Larangan Keras (Non-Negotiable Rules)

1. ❌ **Dilarang menaruh logika bisnis di Controller atau Route closure.** Controller hanya bertindak sebagai adapter tipis: validasi via FormRequest → panggil 1 Action/Query → return Inertia response / API Resource.
2. ❌ **Dilarang menggunakan JavaScript tanpa tipe atau `any` di TypeScript.** Seluruh kode frontend harus TypeScript strict.
3. ❌ **Dilarang menginstall atau menggunakan Axios.** Gunakan router/useHttp native bawaan Inertia.js v3.
4. ❌ **Dilarang query Eloquent panjang di route closure atau komponen React.** Selalu lewat Query Object, Repository, atau Action di Domain layer.
5. ❌ **Dilarang hardcode credential, token, secret, atau password.** Semua via `.env` dan `config/`. Sediakan `.env.example` yang lengkap.
6. ❌ **Dilarang destructive command** (drop database, force push, penghapusan file massal) tanpa konfirmasi pengguna.
7. ❌ **Dilarang menambah library/dependency tanpa alasan konkret dan persetujuan.**
8. ❌ **Dilarang menggunakan Float untuk nilai moneter.** Nilai mata uang selalu integer Rupiah.
9. ❌ **Dilarang otorisasi `if ($user->role === 'admin')` di controller.** Selalu gunakan Laravel Policy / Gate dan Spatie Permissions.

---

## 4. 🛡️ Sumber Daya Terlindungi (Protected Resources)

Dilarang menghapus, memindahkan, atau menimpa berkas berikut tanpa persetujuan eksplisit:
- `docs/` (Dokumentasi arsitektur, API, database, observability, security, brand)
- `public/images/` (`logo.jpg`, `logo-horizontal.png`)
- `requirements.md` (System Requirements Specification)
- `AGENTS.md` (Aturan agen ini)
- `.agents/` (Konfigurasi subagent & skills)

---

## 5. 🔄 Alur Kerja Milestone (Milestone Workflow)

Untuk setiap milestone:
1. Periksa status repositori saat ini (`git status`).
2. Baca spesifikasi kebutuhan terkait di `requirements.md` dan `unneskost-prompt-final.md`.
3. Tinjau ketersediaan dependensi di registry resmi.
4. Buat rencana implementasi teknis di `implementation_plan.md`.
5. Pecah pekerjaan menjadi tugas-tugas kecil yang dapat diverifikasi secara independen.
6. Implementasikan satu task.
7. Jalankan verifikasi nyata (test, lint, type check).
8. Perbaiki kegagalan sebelum melanjutkan ke task berikutnya.
9. Tinjau implementasi dengan prinsip Ponytail (anti-bloat & YAGNI).
10. Terapkan Taste Skill jika tugas melibatkan UI/UX.
11. Jalankan verifikasi menyeluruh untuk milestone bersangkutan.
12. Periksa `git status` dan `git diff --stat`.
13. Perbarui `tasks.md` dan buat laporan hasil verifikasi.

---

## 6. ⚖️ Aturan Pengambilan Keputusan Arsitektur

Jika terdapat konflik antara kebutuhan spesifikasi dan kenyamanan implementasi:
1. Pertahankan spesifikasi kebutuhan proyek secara eksplisit.
2. Pertahankan integritas data dan keamanan sistem.
3. Utamakan solusi bawaan framework (*framework-native*).
4. Pilih arsitektur yang paling sederhana (*simplest architecture*).
5. Minta persetujuan pengguna jika keputusan akan mengubah arsitektur secara signifikan.

---

## 7. 🚦 Definisi Selesai (Definition of Done)

Sebuah task / milestone dinyatakan selesai HANYA JIKA:
- [x] Kode implementasi telah dibuat dan berfungsi.
- [x] Unit test & Feature test (Pest) hijau.
- [x] Static analysis (PHPStan Level Max) bersih tanpa eror (`[OK] No errors`).
- [x] Formatter (Laravel Pint) bersih (`passed`).
- [x] TypeScript type checking (`tsc --noEmit`) bersih (0 error).
- [x] Linter (`npm run lint`) bersih (0 warning/error).
- [x] Build asset (`npm run build`) berhasil.
- [x] Protected resources tetap utuh.
- [x] Tidak ada abstraksi spekulatif / folder kosong yang tidak perlu.
- [x] Git diff hanya memuat perubahan yang diinginkan.
