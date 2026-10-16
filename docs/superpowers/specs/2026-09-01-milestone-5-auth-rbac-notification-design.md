# 📐 Design Specification: Milestone 5 — Autentikasi, RBAC & Notifikasi

- **Tanggal:** 2026-09-01
- **Bounded Contexts:** `App\Domain\User\`, `App\Domain\Auth\`, `App\Domain\Notification\`
- **Status:** In Progress
- **Referensi Kebutuhan:** [`requirements.md`](file:///c:/projects/unneskost2/requirements.md) (FR-AUT-001 s.d. FR-AUT-008, FR-NOT-001 s.d. FR-NOT-005).

---

## 1. Arsitektur Domain & Boundary

```
app/
├── Domain/
│   ├── User/
│   │   ├── Actions/
│   │   │   ├── RegisterUserAction.php
│   │   │   └── UpdateUserProfileAction.php
│   │   ├── DTOs/
│   │   │   ├── RegisterUserData.php
│   │   │   └── UserProfileData.php
│   │   ├── Enums/
│   │   │   └── UserRole.php (admin, owner, tenant)
│   │   └── Policies/
│   │       ├── ListingPolicy.php
│   │       ├── RoomPolicy.php
│   │       ├── BookingPolicy.php
│   │       └── ReviewPolicy.php
│   └── Notification/
│       ├── Notifications/
│       │   ├── BookingCreatedNotification.php
│       │   ├── BookingStatusChangedNotification.php
│       │   └── PaymentConfirmedNotification.php
│       └── Actions/
│           └── MarkNotificationAsReadAction.php
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── AuthenticatedSessionController.php
│   │   │   └── RegisteredUserController.php
│   │   ├── Profile/
│   │   │   └── ProfileController.php
│   │   └── Notification/
│   │       └── NotificationController.php
│   └── Requests/
│       ├── Auth/{LoginRequest, RegisterRequest}.php
│       └── Profile/UpdateProfileRequest.php
```

---

## 2. Invariant & Aturan Keamanan

1. **Strict Spatie RBAC & Policies:**
   - Dilarang `if ($user->role === 'admin')` di controller.
   - Semua otorisasi hak akses menggunakan Policy `$this->authorize(...)` atau `$user->can(...)`.
   - Peran utama:
     - `tenant` (*Mahasiswa / Pencari Kos*): Boleh booking, bookmark wishlist, memberi ulasan atas transaksi sewa miliknya.
     - `owner` (*Pemilik Kos*): Boleh CRUD properti kos miliknya, kelola kamar, setujui/tolak booking masuk, balas ulasan.
     - `admin` (*Pengelola Platform UNNESKost*): Akses penuh ke back-office, moderasi listing, monitoring transaksi.
2. **Multi-Role Registration:**
   - Form registrasi menyediakan opsi peran eksplisit: **Pencari Kos (Mahasiswa UNNES)** atau **Pemilik Kos (Mitra Pemilik)**.
   - Validasi email unik, password terenkripsi (`bcrypt`), nomor WhatsApp/telepon.
3. **Multi-Channel Notification System:**
   - Tabel standar Laravel `notifications` (UUID PK, `notifiable_type`, `notifiable_id`, `type`, `data` jsonb, `read_at`, `created_at`).
   - Saluran `database` untuk lonceng in-app di antarmuka web.
   - Event trigger otomatis:
     - Booking dibuat $\rightarrow$ Notifikasi ke Pemilik Kos.
     - Booking disetujui/ditolak $\rightarrow$ Notifikasi ke Penyewa.
     - Pembayaran lunas $\rightarrow$ Notifikasi ke Penyewa & Pemilik.

---

## 3. Frontend Experience (React 19 + Taste Skill)

1. **Halaman Autentikasi (`Auth/Login.tsx`, `Auth/Register.tsx`):**
   - Desain bersih minimalis dengan palet Coral `#FF6565` dan Slate Navy `#0F172A`.
   - Pilihan tab peran interaktif saat registrasi.
2. **Komponen Lonceng Notifikasi (`NotificationBell.tsx`):**
   - Badge merah dengan jumlah pesan belum dibaca (*unread count*).
   - Popover interaktif dengan daftar notifikasi terbaru dan tombol *Tandai Sudah Dibaca*.
3. **Menu Profil Navigasi (`PublicLayout.tsx` & Navbar):**
   - User profile dropdown: Nama, Role Badge, Menu Dashboard Sesuai Peran, Edit Profil, dan Tombol Logout.
4. **Halaman Profil (`Profile/Edit.tsx`):**
   - Form ubah nama, email, nomor WhatsApp, dan ganti password.
