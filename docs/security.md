# 🛡️ Security Foundation Guide — UNNESKost v3

Dokumen ini mendokumentasikan langkah-langkah, kebijakan, dan implementasi keamanan (*Security by Design*) yang diterapkan pada UNNESKost v3.

---

## 1. Prinsip Keamanan Utama

1. **Defense in Depth:** Keamanan diterapkan di setiap lapisan (Infrastruktur, Jaringan, HTTP, Aplikasi, dan Database).
2. **Principle of Least Privilege:** Setiap peran pengguna hanya memiliki akses minimum yang dibutuhkan untuk menjalankan tugasnya.
3. **Never Trust User Input:** Seluruh data yang masuk wajib divalidasi dan disanitasi secara ketat.

---

## 2. Lapisan Keamanan Aplikasi

### 2.1 Validasi Input (Input Validation & Sanitization)
- Seluruh endpoint HTTP wajib dilindungi oleh `FormRequest` khusus dengan validasi aturan ketat.
- Form di sisi frontend memvalidasi data menggunakan skema `Zod` sebelum request dikirim ke backend.
- File upload (gambar foto kos) divalidasi tipe MIME (`image/jpeg`, `image/png`, `image/webp`), ukuran maksimal (maks 2MB per gambar), dan disanitasi nama filenya dengan UUID unik sebelum disimpan.

### 2.2 Autentikasi & Otorisasi Berbasis Peran (RBAC)
- Autentikasi sesi menggunakan Laravel Sanctum dengan proteksi session cookie `HttpOnly`, `SameSite=Lax`, dan `Secure` (pada lingkungan production).
- Pengelolaan hak akses menggunakan **Spatie Laravel-Permission**:
  - `admin`: Mengelola seluruh kos, moderasi ulasan, manajemen akun.
  - `owner`: Hanya dapat mengelola listing dan kamar miliknya sendiri.
  - `user` (Tenant): Menjelajah, melakukan booking kamar, memberikan ulasan sewa kos, menyimpan favorit.
- **Wajib menggunakan Laravel Policy**: Otorisasi data tidak boleh menggunakan `if ($user->role === ...)` di controller, melainkan via `$this->authorize('update', $listing)` atau Policy helper.

### 2.3 Pencegahan Vulnerability Umum (OWASP Top 10)

| Ancaman | Mitigasi yang Diterapkan |
|---|---|
| **SQL Injection** | Menggunakan parameter binding bawaan PDO/Eloquent. Dilarang menggunakan raw query tanpa binding parameter. |
| **Cross-Site Scripting (XSS)** | React & Blade otomatis melakukan string escaping. Dilengkapi Content-Security-Policy (CSP) headers. |
| **Cross-Site Request Forgery (CSRF)** | Token CSRF otomatis diverifikasi oleh middleware Laravel untuk semua request state-changing (`POST`, `PUT`, `DELETE`). |
| **Mass Assignment** | Model Eloquent wajib mendefinisikan `$fillable` secara eksplisit dan ketat (atau `$guarded = ['*']`). |
| **Race Conditions (Double Booking)** | Menggunakan Database Transaction + Row Locking (`lockForUpdate`) serta constraint PostgreSQL `btree_gist` pada rentang tanggal sewa. |
| **Brute Force & DoS** | Rate limiting diterapkan pada rute login (`throttle:login`), registrasi, dan endpoint public API (`throttle:api`). |

---

## 3. Manajemen Rahasia & Credential

- Dilarang keras menaruh API key, password database, APP_KEY, atau secret token langsung di dalam kode atau Git repository.
- Seluruh secret dikelola via environment variables (`.env`).
- File `.env.example` disediakan secara lengkap tanpa menyertakan secret asli.
- Audit dependensi dilakukan secara berkala melalui pipeline CI:
  - `composer audit`
  - `npm audit`

---

## 4. Keamanan Infrastruktur & Container

- Container Docker dijalankan dengan user non-root (`www-data` / `appuser`).
- File `.dockerignore` memfilter folder `.git`, `.env`, `node_modules`, dan file sensitif lainnya agar tidak masuk ke dalam image Docker.
- Nginx dikonfigurasi dengan HTTP Security Headers:
  - `X-Frame-Options: SAMEORIGIN`
  - `X-Content-Type-Options: nosniff`
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - `Permissions-Policy: geolocation=(self), camera=()`
