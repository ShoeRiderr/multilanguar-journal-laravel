#!/bin/bash

# Skrypt do ręcznego deploymentu aplikacji Laravel na produkcji
# Użycie: ./deploy.sh

set -e  # Zatrzymaj skrypt przy błędzie

echo "🚀 Rozpoczynanie deploymentu na produkcję..."
echo ""

# Sprawdź czy jesteś na branch main
CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD)
if [ "$CURRENT_BRANCH" != "main" ]; then
    echo "⚠️  Ostrzeżenie: Nie jesteś na branch main (obecny: $CURRENT_BRANCH)"
    read -p "Czy chcesz kontynuować? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo "❌ Deployment anulowany"
        exit 1
    fi
fi

# Pull najnowszych zmian
echo "📥 Pobieranie najnowszych zmian z Git..."
git pull origin main

# Stop kontenerów
echo "🛑 Zatrzymywanie kontenerów..."
docker compose down

# Rebuild kontenerów bez cache
echo "🔨 Budowanie kontenerów (bez cache)..."
docker compose build --no-cache

# Uruchomienie kontenerów
echo "🚀 Uruchamianie kontenerów w tle..."
docker compose up -d

# Czekaj na uruchomienie serwisów
echo "⏳ Czekanie na uruchomienie serwisów..."
sleep 10

# Sprawdź status kontenerów
echo "📋 Status kontenerów:"
docker compose ps

# Uruchom migracje
echo ""
echo "📊 Uruchamianie migracji bazy danych..."
docker compose exec app php artisan migrate --force

# Clear i cache konfiguracji
echo ""
echo "⚡ Optymalizacja aplikacji..."
docker compose exec app php artisan config:clear
docker compose exec app php artisan config:cache

# Cache routes
echo "🛣️  Cachowanie routes..."
docker compose exec app php artisan route:cache

# Cache views
echo "👁️  Cachowanie views..."
docker compose exec app php artisan view:cache

# Optimize
echo "⚡ Uruchamianie optymalizacji..."
docker compose exec app php artisan optimize

# Restart kolejek
echo "🔄 Restart worker'ów kolejek..."
docker compose exec app php artisan queue:restart

echo ""
echo "✅ Deployment zakończony pomyślnie!"
echo ""
echo "📋 Przydatne komendy:"
echo "   - Logi: docker compose logs -f"
echo "   - Status: docker compose ps"
echo "   - Shell: docker compose exec app bash"
echo "   - MySQL: docker compose exec mysql mysql -u root -p"
echo ""
