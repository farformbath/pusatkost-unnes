# 🔌 REST API Specification — UNNESKost v3 (`/api/v1`)

> Dokumen spesifikasi kontrak antarmuka pemrograman aplikasi (REST API) sekunder untuk keperluan integrasi mobile app dan layanan pihak ketiga.

---

## 1. Prinsip Utama API

1. **Sekunder & Terarah:** Antarmuka web utama menggunakan protokol Inertia.js v3. REST API `/api/v1` dirancang khusus untuk mobile client masa depan.
2. **Autentikasi Sanctum Token:** Menggunakan Bearer token (`Authorization: Bearer <token>`).
3. **Standar JSON Envelope Konsisten:** Semua respon memiliki struktur payload JSON terstandarisasi.
4. **FormRequest Validation & API Resources:** Dilarang mengembalikan model Eloquent secara langsung tanpa API Resource transformer.
5. **Rate Limiting:** Dilindungi middleware rate limiter `throttle:api` (60 request/menit untuk unauthenticated, 120 request/menit untuk authenticated).

---

## 2. Struktur Respon JSON (Envelope Standard)

### Respon Sukses (Success Response):
```json
{
  "success": true,
  "message": "Detail kos berhasil diambil.",
  "data": {
    "id": "9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d",
    "nama": "Kost Putri Sekaran Asri",
    "slug": "kost-putri-sekaran-asri",
    "jenis_kos": "putri",
    "harga_mulai": 650000,
    "alamat": "Jl. Taman Siswa No. 12, Sekaran, Gunungpati, Semarang",
    "latitude": -7.0512345,
    "longitude": 110.4056789,
    "rooms": []
  },
  "meta": {
    "timestamp": "2026-08-30T20:55:00Z",
    "correlation_id": "req-9b1deb4d-3b7d-4bad-9bdd"
  }
}
```

### Respon Sukses Berhalaman (Paginated Response):
```json
{
  "success": true,
  "message": "Daftar kos berhasil diambil.",
  "data": [ /* item array */ ],
  "pagination": {
    "current_page": 1,
    "per_page": 15,
    "total": 45,
    "last_page": 3,
    "next_page_url": "https://api.unneskost.com/api/v1/listings?page=2",
    "prev_page_url": null
  },
  "meta": {
    "timestamp": "2026-08-30T20:55:00Z",
    "correlation_id": "req-9b1deb4d-3b7d-4bad-9bdd"
  }
}
```

### Respon Eror (Error Response):
```json
{
  "success": false,
  "message": "Data yang dikirimkan tidak valid.",
  "errors": {
    "room_id": ["Kamar yang dipilih tidak tersedia untuk rentang tanggal tersebut."]
  },
  "meta": {
    "timestamp": "2026-08-30T20:55:00Z",
    "correlation_id": "req-9b1deb4d-3b7d-4bad-9bdd"
  }
}
```

---

## 3. Daftar Endpoint Inti

### 3.1 Autentikasi (`/api/v1/auth`)
- `POST /api/v1/auth/login` — Login pengguna & dapatkan token Sanctum.
- `POST /api/v1/auth/register` — Registrasi tenant/user baru.
- `POST /api/v1/auth/logout` — Revoke token aktif.
- `GET  /api/v1/auth/me` — Ambil profil pengguna yang sedang login.

### 3.2 Listing & Pencarian (`/api/v1/listings`)
- `GET  /api/v1/listings` — Ambil katalog kos (dengan query search, filter harga, fasilitas, jenis kos).
- `GET  /api/v1/listings/{id}` — Ambil detail lengkap kos, galeri foto, fasilitas, dan daftar kamar.

### 3.3 Kamar (`/api/v1/rooms`)
- `GET  /api/v1/rooms/{id}` — Ambil detail kamar dan ketersediaan sewa.

### 3.4 Booking (`/api/v1/bookings`)
- `GET  /api/v1/bookings` — Ambil daftar riwayat booking milik pengguna login.
- `POST /api/v1/bookings` — Buat booking kamar baru (memeriksa ketersediaan & lock).
- `GET  /api/v1/bookings/{id}` — Ambil detail booking & instruksi pembayaran.
- `POST /api/v1/bookings/{id}/cancel` — Batalkan booking yang masih pending.

### 3.5 Favorit / Wishlist (`/api/v1/favorites`)
- `GET  /api/v1/favorites` — Ambil daftar kos yang difavoritkan pengguna.
- `POST /api/v1/favorites/{listing_id}/toggle` — Tambah / hapus kos dari favorit.
