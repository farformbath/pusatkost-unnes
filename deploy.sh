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
docker compose -f docker-compose.prod.yml up -d --force-recreate

# 4. Bersihkan config cache lama & tunggu kesiapan database
echo "⏳ 4/6 Menyiapkan konfigurasi & koneksi database..."
docker compose -f docker-compose.prod.yml exec -T app php artisan config:clear || true

docker compose -f docker-compose.prod.yml exec -T app php -r '
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();
for ($i = 0; $i < 30; $i++) {
    try {
        Illuminate\Support\Facades\DB::connection()->getPdo();
        echo "Database connected!\n";
        exit(0);
    } catch (\Throwable $e) {
        sleep(2);
    }
}
exit(1);
' || echo "Melanjutkan ke migrasi..."

echo "📦 5/6 Menjalankan migrasi database..."
docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force || docker compose -f docker-compose.prod.yml exec -T app php artisan migrate:fresh --force
docker compose -f docker-compose.prod.yml exec -T app php artisan storage:link || true

# 6. Optimasi cache produksi
echo "⚡ 6/6 Mengaktifkan cache produksi (config, route, view)..."
docker compose -f docker-compose.prod.yml exec -T app php artisan config:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan route:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan view:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan queue:restart

echo "✅ [UNNESKost v3] Deployment Selesai dengan Sukses!"
