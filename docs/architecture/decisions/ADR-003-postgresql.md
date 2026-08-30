# ADR-003: Pemilihan PostgreSQL 16 sebagai Basis Data Utama

- **Status:** Diterima (Accepted)
- **Tanggal:** 2026-08-30
- **Pengambil Keputusan:** Principal Software Architect & Lead Database Engineer

---

## 1. Konteks & Masalah

UNNESKost v3 membutuhkan RDBMS yang handal dalam menangani:
1. Integritas data tingkat tinggi untuk menghindari double booking.
2. Fitur geolokasi (query jarak radius kos ke kampus UNNES).
3. Dukungan native UUID v7 / v4.
4. Dukungan tipe data Enum native dan JSONB terindeks.

---

## 2. Analisis Keputusan

- **MySQL / MariaDB:** Sangat populer, namun dukungan exclusion constraints (`btree_gist`) untuk mencegah overlap rentang tanggal sewa tidak sefleksibel dan sekuat PostgreSQL.
- **PostgreSQL 16:** Memiliki dukungan native UUID yang efisien, native enum types, full ACID compliance, PostGIS/GiST indexing untuk query spasial titik koordinat kos, serta exclusion constraints tingkat database.

---

## 3. Keputusan

Kami memilih **PostgreSQL 16**:
- Seluruh primary key menggunakan `UUID`.
- Mata uang disimpan sebagai `BIGINT` (Rupiah bulat).
- Status entitas menggunakan PostgreSQL Native Enum.
- Integritas booking dilindungi oleh transaksi locking dan constraint geospasial/exclusion.

---

## 4. Konsekuensi

- **Positif:** Keandalan transaksi sangat terjamin, zero double-booking di level database, performa indexing tinggi untuk koordinat latitude/longitude.
- **Negatif:** Sedikit perbedaan konfigurasi driver PDO pada mesin lokal developer, yang diatasi secara mulus menggunakan Docker Compose / WSL2.
