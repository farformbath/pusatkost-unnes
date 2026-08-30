# ADR-001: Pemilihan Arsitektur Modular Monolith dengan Pola DDD-Lite

- **Status:** Diterima (Accepted)
- **Tanggal:** 2026-08-30
- **Pengambil Keputusan:** Principal Software Architect

---

## 1. Konteks & Masalah

UNNESKost v3 membutuhkan struktur sistem yang terorganisir rapi, mudah diuji (*testable*), memiliki batas domain yang jelas antar fitur (Listing, Booking, Review, Auth), namun tidak menimbulkan beban operasional berlebihan seperti microservices.

Terdapat 3 opsi arsitektur utama:
1. **Traditional Monolithic MVC (Fat Controller / Fat Model)**
2. **Microservices Architecture**
3. **Modular Monolith dengan DDD-Lite & Action Pattern**

---

## 2. Analisis Keputusan

- **Traditional MVC:** Seringkali berujung pada *God Models* dan logika bisnis berceceran di controller atau route closure, menyulitkan pemeliharaan dan unit testing.
- **Microservices:** Terlalu dini (*premature optimization*), menambah kompleksitas distributed transaction, network overhead, dan orkestrasi infrastruktur yang rumit.
- **Modular Monolith (DDD-Lite):** Menjaga kemudahan single codebase dan single deployment pipeline, namun mengelompokkan logika bisnis ke dalam Bounded Context (`app/Domain/`) yang mandiri.

---

## 3. Keputusan

Kami memilih **Modular Monolith dengan DDD-Lite**:
- Logika bisnis utama diisolasi di `app/Domain/{Context}/` dan bersifat bebas dari dependensi HTTP.
- Controller bertindak hanya sebagai adaptor tipis (*thin controller*).
- Komunikasi antar konteks menggunakan Domain Events atau Service Contracts.

---

## 4. Konsekuensi

- **Positif:** Kecepatan development tinggi, zero latency antar modul, unit test domain murni tanpa mock HTTP, dan siap diekstrak menjadi microservices jika traffic menuntut di masa depan.
- **Negatif:** Memerlukan disiplin developer untuk mematuhi boundary domain dan tidak melakukan query lintas batas secara sembarangan.
