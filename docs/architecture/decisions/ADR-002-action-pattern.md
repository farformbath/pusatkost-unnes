# ADR-002: Penerapan Action Pattern dan Single Responsibility di Domain Layer

- **Status:** Diterima (Accepted)
- **Tanggal:** 2026-08-30
- **Pengambil Keputusan:** Principal Software Architect

---

## 1. Konteks & Masalah

Di aplikasi Laravel berskala menengah ke atas, logika transaksi bisnis sering kali ditaruh di dalam Controller atau Service class raksasa (*God Service Class*) yang berisi puluhan method yang saling tumpang tindih. Hal ini menyebabkan kode sulit diuji, rawan konflik saat kolaborasi, dan sulit dilacak alurnya.

---

## 2. Analisis Keputusan

- **Opsi 1: Controller-based logic** $\rightarrow$ Menyebabkan *fat controller*, tidak dapat digunakan kembali di REST API atau Command Artisan.
- **Opsi 2: Giant Service Classes** (`BookingService` dengan 20 method) $\rightarrow$ Sulit di-maintain, class membesar, SRP (*Single Responsibility Principle*) terlanggar.
- **Opsi 3: Action Pattern (1 Class = 1 Operasi Bisnis)** $\rightarrow$ Setiap transaksi (misal `BookRoomAction`, `CancelBookingAction`) memiliki class mandiri dengan 1 public method `handle()`.

---

## 3. Keputusan

Kami memilih **Action Pattern** untuk semua operasi bisnis di Domain layer:
- Setiap Action menerima parameter berupa **DTO (Data Transfer Object)** atau tipe data primitif.
- Action tidak boleh memanggil fungsi HTTP seperti `request()`, `auth()`, `session()`, atau `redirect()`.
- Efek samping di-dispatch via Domain Events.

---

## 4. Konsekuensi

- **Positif:** Keterbacaan alur bisnis sangat tinggi, mudah di-unit test secara terisolasi, dapat dipanggil dari mana saja (Inertia controller, REST API, Artisan command, atau Queue worker).
- **Negatif:** Jumlah file class bertambah, namun struktur direktori menjadi sangat teratur dan mudah dicari.
