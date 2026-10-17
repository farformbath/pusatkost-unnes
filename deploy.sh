#!/bin/bash
set -e

echo "🚀 [UNNESKost v3] Memulai Deployment Production..."

# 1. Pull pembaruan terbaru dari GitHub
echo "📥 1/7 Menarik kode terbaru dari Git..."
git pull origin main

# 2. Build image container (Multi-stage: Node build + Composer build + PHP-FPM)
echo "🔨 2/7 Melakukan build Docker Image..."
docker compose -f docker-compose.prod.yml build --pull

# 3. Jalankan container di background
echo "🔄 3/7 Menyalakan container services..."
docker compose -f docker-compose.prod.yml up -d --force-recreate

# 4. Salin aset frontend hasil build dari container ke host ./public/build untuk Nginx
echo "📦 4/7 Menyinkronkan aset public/build ke host..."
mkdir -p ./public/build
docker compose -f docker-compose.prod.yml cp app:/var/www/html/public/build/. ./public/build/

# 5. Setup folder dan permission storage agar www-data bisa menulis
echo "🔒 5/7 Memverifikasi permission storage & bootstrap cache..."
docker compose -f docker-compose.prod.yml exec -T -u 0 app sh -c "
mkdir -p /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
"

# 6. Periksa dan generate APP_KEY jika belum disetel di .env
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    echo "🔑 APP_KEY belum disetel. Menghasilkan APP_KEY baru..."
    NEW_KEY=$(docker compose -f docker-compose.prod.yml exec -T app php artisan key:generate --show)
    if grep -q "^APP_KEY=" .env; then
        sed -i "s|^APP_KEY=.*|APP_KEY=${NEW_KEY}|" .env
    else
        echo "APP_KEY=${NEW_KEY}" >> .env
    fi
    echo "✅ APP_KEY berhasil disetel di .env!"
    # Recreate app agar membaca .env yang telah diperbarui
    docker compose -f docker-compose.prod.yml up -d --force-recreate app queue-worker scheduler
fi

# 7. Bersihkan config cache lama & tunggu kesiapan database
echo "⏳ 6/7 Menyiapkan konfigurasi & koneksi database..."
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

# 8. Jalankan migrasi dan storage link
docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force
docker compose -f docker-compose.prod.yml exec -T app php artisan storage:link || true

# 9. Optimasi cache produksi
echo "⚡ 7/7 Mengaktifkan cache produksi (config, route, view)..."
docker compose -f docker-compose.prod.yml exec -T app php artisan config:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan route:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan view:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan queue:restart

# 10. Reload Nginx
docker compose -f docker-compose.prod.yml exec -T nginx nginx -s reload || true

echo "✅ [UNNESKost v3] Deployment Selesai dengan Sukses!"
