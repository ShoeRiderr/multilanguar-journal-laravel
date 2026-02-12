# Makefile dla zarządzania Docker Compose
# Skróty dla najczęściej używanych komend

.PHONY: help build up down restart logs shell mysql redis test clean deploy

# Domyślna komenda - wyświetl pomoc
help:
	@echo "📚 Dostępne komendy:"
	@echo ""
	@echo "  make build      - Zbuduj wszystkie kontenery Docker"
	@echo "  make up         - Uruchom wszystkie kontenery w tle"
	@echo "  make down       - Zatrzymaj wszystkie kontenery"
	@echo "  make restart    - Restart wszystkich kontenerów"
	@echo "  make logs       - Wyświetl logi wszystkich kontenerów"
	@echo "  make shell      - Otwórz bash w kontenerze app"
	@echo "  make mysql      - Otwórz MySQL shell"
	@echo "  make redis      - Otwórz Redis CLI"
	@echo "  make test       - Uruchom testy aplikacji"
	@echo "  make clean      - Zatrzymaj i usuń kontenery + volumes"
	@echo "  make deploy     - Wykonaj deployment (produkcja)"
	@echo "  make optimize   - Zoptymalizuj aplikację (cache)"
	@echo "  make migrate    - Uruchom migracje bazy danych"
	@echo "  make fresh      - Fresh install (rebuild + migrate)"
	@echo ""

# Zbuduj wszystkie kontenery
build:
	@echo "🔨 Budowanie kontenerów..."
	docker compose build

# Uruchom kontenery w tle
up:
	@echo "🚀 Uruchamianie kontenerów..."
	docker compose up -d
	@echo "✅ Kontenery uruchomione!"
	@make status

# Zatrzymaj kontenery
down:
	@echo "🛑 Zatrzymywanie kontenerów..."
	docker compose down
	@echo "✅ Kontenery zatrzymane!"

# Restart kontenerów
restart:
	@echo "🔄 Restart kontenerów..."
	@make down
	@make up

# Wyświetl logi
logs:
	docker compose logs -f

# Logi tylko z app
logs-app:
	docker compose logs -f app

# Logi tylko z nginx
logs-nginx:
	docker compose logs -f nginx

# Status kontenerów
status:
	@echo "📋 Status kontenerów:"
	docker compose ps

# Bash w kontenerze app
shell:
	docker compose exec app bash

# MySQL shell
mysql:
	docker compose exec mysql mysql -u root -p

# Redis CLI
redis:
	docker compose exec redis redis-cli

# Uruchom testy
test:
	@echo "🧪 Uruchamianie testów..."
	docker compose exec app php artisan test

# Migracje
migrate:
	@echo "📊 Uruchamianie migracji..."
	docker compose exec app php artisan migrate

# Migracje fresh (reset bazy)
migrate-fresh:
	@echo "⚠️  Reset bazy danych..."
	docker compose exec app php artisan migrate:fresh --seed

# Optymalizacja aplikacji
optimize:
	@echo "⚡ Optymalizacja aplikacji..."
	docker compose exec app php artisan config:cache
	docker compose exec app php artisan route:cache
	docker compose exec app php artisan view:cache
	docker compose exec app php artisan optimize

# Clear cache
clear:
	@echo "🧹 Czyszczenie cache..."
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan route:clear
	docker compose exec app php artisan view:clear
	docker compose exec app php artisan cache:clear
	docker compose exec app php artisan optimize:clear

# Fresh install
fresh:
	@echo "🔄 Fresh install..."
	@make build
	@make up
	@sleep 5
	@make migrate-fresh
	@echo "✅ Fresh install zakończony!"

# Czyszczenie (stop + remove volumes)
clean:
	@echo "🧹 Czyszczenie kontenerów i volumes..."
	docker compose down -v
	@echo "✅ Wyczyszczono!"

# Deployment (produkcja)
deploy:
	@echo "🚀 Deployment na produkcję..."
	./deploy.sh

# Composer install
composer-install:
	docker compose exec app composer install

# NPM install
npm-install:
	docker compose exec app npm install

# NPM build
npm-build:
	docker compose exec app npm run build

# Generuj klucz aplikacji
key-generate:
	docker compose exec app php artisan key:generate

# Storage link
storage-link:
	docker compose exec app php artisan storage:link

# Queue work
queue:
	docker compose exec app php artisan queue:work

# Tinker
tinker:
	docker compose exec app php artisan tinker
