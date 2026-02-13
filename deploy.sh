#!/bin/bash
# Skrypt do ręcznego deploymentu aplikacji na Hetzner Cloud
# Użycie: ./deploy.sh

set -e

echo "🚀 Deploying to Hetzner Cloud..."

# Pull najnowszych zmian
echo "📥 Pulling latest changes from git..."
git pull origin main

echo "📦 Building assets..."
# Zbuduj assety NA HOŚCIE (gdzie PHP i Node są dostępne)
npm ci
SKIP_WAYFINDER=false npm run build

# Sprawdź czy build się udał
if [ ! -d "public/build" ]; then
    echo "❌ Build failed - public/build not found!"
    exit 1
fi

echo "✅ Assets built successfully!"

# Zatrzymaj kontenery
echo "🛑 Stopping containers..."
docker compose down

# Zbuduj nowy obraz aplikacji
echo "🔨 Building application image..."
docker compose build --no-cache app

# Uruchom kontenery
echo "🚀 Starting containers..."
docker compose up -d

# Poczekaj na MySQL
echo "⏳ Waiting for MySQL to be ready..."
until docker compose exec -T mysql mysqladmin ping -h localhost --silent 2>/dev/null; do
    echo "MySQL is unavailable - sleeping"
    sleep 2
done

echo "✅ MySQL is up!"

# Uruchom migracje
echo "🔄 Running database migrations..."
docker compose exec -T app php artisan migrate --force

# Optymalizacja Laravel
echo "⚡ Optimizing Laravel..."
docker compose exec -T app php artisan optimize
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan route:cache
docker compose exec -T app php artisan view:cache

# Wygeneruj Wayfinder routes (teraz PHP jest dostępne)
echo "🗺️ Generating Wayfinder routes..."
docker compose exec -T app php artisan wayfinder:generate --with-form || echo "⚠️ Wayfinder generation skipped"

# Restart queue workers
echo "🔄 Restarting queue workers..."
docker compose exec -T app php artisan queue:restart || true

# Czyszczenie starych obrazów Docker
echo "🧹 Cleaning up old Docker images..."
docker system prune -f

# Pokaż status kontenerów
echo ""
echo "✅ Deployment complete!"
echo ""
echo "📊 Container status:"
docker compose ps

echo ""
echo "🎉 Application is ready!"
echo "Check logs with: docker compose logs -f"
