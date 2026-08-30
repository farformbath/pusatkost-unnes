# 🏛️ Architecture Overview — UNNESKost v3

Dokumen ini menjelaskan rancangan arsitektur modular monolith pada UNNESKost v3, batasan *bounded context*, interaksi antar modul, serta pemisahan tanggung jawab (*Separation of Concerns*).

---

## 1. Konsep Modular Monolith & DDD-Lite

UNNESKost v3 menggunakan pendekatan **Modular Monolith** dengan pola **Domain-Driven Design Lite (DDD-lite)** dan **Action Pattern**:
- Kode bisnis utama diisolasi di dalam `app/Domain/` berdasarkan Bounded Context.
- Modul-modul domain **tidak memiliki ketergantungan pada HTTP request/response** (`request()`, `session()`, `redirect()` dilarang di Domain layer).
- Setiap operasi bisnis diimplementasikan dalam 1 class mandiri (**Action Pattern**).

---

## 2. Bounded Context (Konteks Modul)

```
app/Domain/
├── Listing/      # Mengelola Kos, Tipe Kamar, Fasilitas, dan Galeri Foto
├── Booking/      # Mengelola Siklus Pemesanan, Pembayaran, dan Audit Status Kamar
├── Review/       # Mengelola Ulasan & Rating oleh Tenant pasca sewa + Moderasi
├── Favorite/     # Mengelola Wishlist kos bagi tenant
├── Search/       # Mengelola Query Pencarian (Scout + Meilisearch) & Database fallback
└── User/         # Mengelola Pengguna, Profil, serta RBAC (Spatie Permissions)
```

### Rincian Modul & Tanggung Jawab:

| Bounded Context | Model Utama | Operasi Bisnis (Actions) | Event & Efek Samping |
|---|---|---|---|
| **Listing** | `Listing`, `Room`, `Facility`, `Image` | `CreateListingAction`, `UpdateRoomStatusAction`, `UploadListingImagesAction` | `ListingCreated`, `RoomStatusChanged` (memicu re-index search) |
| **Booking** | `Booking`, `Payment`, `RoomStatusAudit` | `BookRoomAction`, `ProcessPaymentAction`, `CancelBookingAction` | `BookingCreated`, `PaymentCompleted`, `BookingCancelled` (notifikasi email & in-app) |
| **Review** | `Review` | `SubmitReviewAction`, `ModerateReviewAction` | `ReviewSubmitted` |
| **Favorite** | `Favorite` | `ToggleFavoriteAction` | - |
| **Search** | Query Objects | `SearchListingsQuery` | Graceful fallback ke PostgreSQL jika Meilisearch offline |
| **User** | `User`, `Profile` | `RegisterUserAction`, `UpdateProfileAction` | `UserRegistered` |

---

## 3. Komunikasi Antar Bounded Context

Untuk menjaga *modular boundary* yang bersih:
1. **Domain Events:** Efek samping lintas konteks dikomunikasikan via event (contoh: `BookingCreated` di-listen oleh listener notifikasi dan listener pembaruan status kamar).
2. **Service Interfaces:** Jika konteks Booking memerlukan validasi ketersediaan kamar, Booking memanggil `RoomAvailabilityService` melalui contract/interface.
3. **Dilarang Direct Cross-Context Mutation:** Modul `Booking` tidak boleh memodifikasi tabel `listings` secara langsung; semua mutasi dilakukan via Action di domain yang bersangkutan.

---

## 4. Alur Kerja (End-to-End Flow)

### 4.1 Alur Booking Kamar (Event-Driven):
```
Client (React Form)
  │
  ▼
[POST /bookings] ──► StoreBookingRequest (Validasi payload)
                        │
                        ▼
                     BookingController@store (Thin Controller)
                        │
                        ▼
                     BookRoomAction::handle(BookRoomDTO)
                        ├── Cek ketersediaan via RoomAvailabilityService (DB Lock & Exclusion Constraint)
                        ├── Simpan record Booking (Status: PENDING)
                        ├── Mutasi status Room -> BOOKED (catat di RoomStatusAudit)
                        └── Dispatch Event: BookingCreated
                               │
                               ├─► SendBookingNotificationListener (Queued via Redis)
                               └─► SyncSearchIndexListener (Queued via Redis)
                        │
                        ▼
                     Redirect with Inertia Flash Notification
```

### 4.2 Alur Pencarian Kos:
```
Client (Search Filter Bar)
  │
  ▼
[GET /listings?search=&price_max=...] ──► PublicListingController@index
                                            │
                                            ▼
                                         SearchListingsQuery
                                            ├── Coba cari via Scout / Meilisearch
                                            └── Jika gagal / down, fallback ke PostgreSQL query
                                            │
                                            ▼
                                         Inertia::render('listings/Index', [...])
```

---

## 5. Model Data & PostgreSQL Constraints

- **Primary Keys:** Menggunakan `UUID` (atau ULID) untuk seluruh tabel utama untuk keamanan dan skalabilitas.
- **Nilai Uang:** Disimpan dalam integer (Rupiah bulat, bukan float).
- **Integritas Booking:** Memanfaatkan PostgreSQL `btree_gist` / exclusion constraint atau row-locking (`lockForUpdate`) di transaksi database untuk mencegah *race condition* double-booking kamar pada rentang tanggal yang sama.
