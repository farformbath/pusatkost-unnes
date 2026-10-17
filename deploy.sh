#!/bin/bash
set -e

echo "🚀 [UNNESKost v3] Memulai Deployment Production..."

# 1. Pull pembaruan terbaru dari GitHub
echo "📥 1/6 Menarik kode terbaru dari Git..."
git pull origin main

# 2. Build image container (Multi-stage: Node build + Composer build + PHP-FPM)
echo "🔨 2/6 Melakukan build Docker Image..."
docker compose -f docker-compose.prod.yml build --pull

# 3. Jalankan container di background
echo "🔄 3/6 Menyalakan container services..."
docker compose -f docker-compose.prod.yml up -d

# 4. Tunggu kesiapan database
echo "⏳ 4/6 Menunggu database siap..."
docker compose -f docker-compose.prod.yml exec -T app php -r '
for ($i = 0; $i < 30; $i++) {
    try {
        DB::connection()->getPdo();
        echo "Database connected!\n";
        exit(0);
    } catch (\Throwable $e) {
        sleep(2);
    }
}
exit(1);
' || echo "Lanjut ke migrasi..."

# 5. Jalankan migrasi dan symlink storage
echo "📦 5/6 Menjalankan migrasi database..."
docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force
docker compose -f docker-compose.prod.yml exec -T app php artisan storage:link || true

# 6. Optimasi cache produksi
echo "⚡ 6/6 Mengaktifkan cache produksi (config, route, view)..."
docker compose -f docker-compose.prod.yml exec -T app php artisan config:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan route:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan view:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan queue:restart

echo "✅ [UNNESKost v3] Deployment Selesai dengan Sukses!"
