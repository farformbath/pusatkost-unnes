# ADR-004: Pemilihan Inertia.js v3 + React 19 tanpa Axios

- **Status:** Diterima (Accepted)
- **Tanggal:** 2026-08-30
- **Pengambil Keputusan:** Principal Software Architect & Frontend Lead

---

## 1. Konteks & Masalah

Untuk membangun antarmuka pengguna (*User Interface*) yang modern, reaktif, dan kaya interaksi, terdapat pilihan antara:
1. **Separate SPA (Pure REST API + React SPA):** Memerlukan pengelolaan token auth ganda, routing terpisah di client dan server, duplikasi penanganan error, dan overhead serialisasi.
2. **Traditional Server-Side Blade:** Kurang interaktif untuk fitur seperti pencarian interaktif, filter instan, optimistik wishlist, dan galeri interaktif.
3. **Inertia.js v3 Monolith Bridge:** Menggabungkan kenyamanan routing & controller Laravel dengan kekuatan komponen React 19.

---

## 2. Analisis Keputusan

- **Inertia.js v3** menyediakan:
  - Protokol komunikasi berbasis JSON tanpa perlu menulis REST API boilerplate untuk antarmuka web utama.
  - Native XHR client (`router.visit()`, `router.post()`, `useForm()`), sehingga **tidak memerlukan Axios**.
  - Fitur *Optimistic UI* dan *Partial Reloads* bawaan.
  - Sinkronisasi tipe data dari PHP ke TypeScript via `spatie/typescript-transformer`.

---

## 3. Keputusan

Kami memilih **Inertia.js v3 + React 19 (TypeScript Strict) + Tailwind CSS v4**:
- Axios dilarang keras di frontend; gunakan native Inertia router client.
- Seluruh form menggunakan validasi `zod` + `react-hook-form` / Inertia form helper.
- REST API `/api/v1` tetap disediakan secara terpisah hanya untuk keperluan integrasi mobile masa depan dengan autentikasi Sanctum token.

---

## 4. Konsekuensi

- **Positif:** Kecepatan pengembangan sangat tinggi, type safety end-to-end tanpa overhead API manual, SEO dan SSR siap diintegrasikan via Vite plugin.
- **Negatif:** Developer frontend perlu memahami konsep Inertia lifecycle (bukan fetch/axios konvensional).
