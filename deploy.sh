#!/bin/bash
set -e

echo "🚀 Deploying to Hetzner Cloud..."
echo ""

# 1. Pull latest code
echo "📥 Pulling latest changes..."
git pull origin main

# 2. Uruchom backend (app + mysql) PRZED buildem assetów
echo "🐳 Starting backend containers (needed for Wayfinder)..."
docker compose up -d app mysql redis

# 3. Poczekaj na MySQL
echo "⏳ Waiting for MySQL to be ready..."
until docker compose exec -T mysql mysqladmin ping -h localhost --silent 2>/dev/null; do
    echo "MySQL is unavailable - sleeping"
    sleep 2
done
echo "✅ MySQL is up!"

# 4. Install Node dependencies (jeśli nie ma node_modules)
if [ ! -d "node_modules" ]; then
    echo "📦 Installing Node dependencies..."
    npm ci
fi

# 5. KLUCZOWE: Build assetów NA HOŚCIE
# Vite może teraz użyć: docker compose exec app php artisan
echo "🔨 Building assets (Wayfinder will use: docker compose exec app php artisan)..."
npm run build

# Sprawdź czy build się udał
if [ ! -d "public/build" ]; then
    echo "❌ Build failed - public/build not found!"
    exit 1
fi
echo "✅ Assets built successfully!"

# 6. Rebuild Docker image (kopiuje zbudowane assety)
echo "🐳 Rebuilding app container with fresh assets..."
docker compose build --no-cache app

# 7. Uruchom wszystkie kontenery
echo "🚀 Starting all containers..."
docker compose up -d

# Poczekaj chwilę na restart
sleep 3

# 8. Migracje
echo "🔄 Running database migrations..."
docker compose exec -T app php artisan migrate --force

# 9. Optymalizacja Laravel
echo "⚡ Optimizing Laravel..."
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan route:cache
docker compose exec -T app php artisan view:cache
docker compose exec -T app php artisan optimize

# 10. Restart queue workers (jeśli używasz)
echo "🔄 Restarting queue workers..."
docker compose exec -T app php artisan queue:restart || true

# 11. Cleanup
echo "🧹 Cleaning up Docker resources..."
docker system prune -f

echo ""
echo "✅ Deployment complete!"
echo ""
echo "📊 Container status:"
docker compose ps

echo ""
echo "💡 Useful commands:"
echo "   Logs:       docker compose logs -f"
echo "   Shell:      docker compose exec app sh"
echo "   Wayfinder:  docker compose exec app php artisan wayfinder:generate --with-form"
