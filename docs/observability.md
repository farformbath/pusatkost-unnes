# 📊 Observability & System Health Guide — UNNESKost v3

Dokumen ini mendefinisikan standar pemantauan sistem (*observability*), kesehatan aplikasi (*health checks*), *structured logging*, dan pelacakan antrean (*queue monitoring*) pada **UNNESKost v3**.

---

## 1. Prinsip Utama Observability

1. **Structured JSON Logging:** Semua log aplikasi di lingkungan staging/produksi diformat dalam JSON standar untuk memudahkan parsing oleh log aggregator di masa depan.
2. **Correlation ID (`X-Request-Id`):** Setiap request HTTP diberikan ID korelasi unik yang diteruskan ke seluruh log, domain event, dan response header.
3. **Dedicated Healthcheck Endpoint (`/health`):** Endpoint JSON publik/internal untuk memantau status runtime database, cache, dan search engine tanpa beban overhead.
4. **Queue & Job Monitoring:** Memantau status antrean pekerjaan via **Laravel Horizon** dan metrik performa aplikasi via **Laravel Pulse**.

---

## 2. Endpoint Health Check (`GET /health`)

Endpoint `/health` digunakan oleh load balancer, uptime monitor, atau container orchestrator (Docker/Kubernetes) untuk memverifikasi kesehatan setiap komponen sistem.

### Contoh Format Respon (`200 OK` / `503 Service Unavailable`):
```json
{
  "status": "healthy",
  "environment": "production",
  "timestamp": "2026-08-30T20:55:00Z",
  "services": {
    "database": {
      "status": "up",
      "latency_ms": 2.4,
      "driver": "pgsql"
    },
    "redis": {
      "status": "up",
      "latency_ms": 0.8
    },
    "meilisearch": {
      "status": "up",
      "latency_ms": 5.1
    },
    "storage": {
      "status": "up",
      "disk": "s3"
    }
  },
  "version": "3.0.0"
}
```

---

## 3. Middleware Correlation ID (`CorrelationIdMiddleware`)

Setiap request yang masuk akan diperiksa header `X-Request-Id`. Jika tidak ada, sistem akan men-generate UUID baru dan menyematkannya ke:
1. `Log::withContext(['correlation_id' => $id])`
2. HTTP Response Header: `X-Request-Id: <id>`
3. Payload metadata pada REST API response.

---

## 4. Structured JSON Logging

Contoh format log output:
```json
{
  "timestamp": "2026-08-30T20:55:01.123456+00:00",
  "level": "INFO",
  "message": "Room status updated",
  "context": {
    "correlation_id": "9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d",
    "room_id": "7c2eeb1a-1a2b-4c3d-8e9f-0a1b2c3d4e5f",
    "old_status": "AVAILABLE",
    "new_status": "BOOKED",
    "changed_by": "user-uuid-123",
    "action": "BookRoomAction"
  }
}
```

---

## 5. Antrean & Metrik Operasional

| Tool | Lingkungan | Fungsi |
|---|---|---|
| **Laravel Horizon** | Semua (Non-Local & Local) | Dashboard pemantauan antrean Redis, throughput, job metrics, dan riwayat `failed_jobs`. |
| **Laravel Telescope** | Khusus Local / Staging | Debugging query database, mail previews, request inspect (dilarang di production). |
| **Laravel Pulse** | Staging & Production | Real-time application performance monitoring (slow queries, slow requests, server CPU & Memory). |
