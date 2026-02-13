.PHONY: help install dev build deploy up down restart logs shell mysql clean wayfinder

help: ## Pokaż pomoc
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

install: ## Pierwsza instalacja (na serwerze)
	@echo "🚀 Installing..."
	cp .env.example .env
	@echo "⚠️  Edytuj .env i ustaw DB_PASSWORD, APP_KEY etc."
	@echo "⚠️  Następnie uruchom: make deploy"

dev: ## Development - uruchom backend i frontend z hot reload
	@echo "🔥 Starting development environment..."
	docker compose up -d app mysql redis
	@echo "📦 Installing npm dependencies..."
	npm install
	@echo "🚀 Starting Vite dev server (hot reload)..."
	npm run dev

build: ## Zbuduj assety (wymaga działającego backendu)
	@echo "🔨 Building assets..."
	docker compose up -d app mysql
	npm ci
	npm run build

deploy: ## Deploy na produkcję (full flow)
	./deploy.sh

up: ## Uruchom wszystkie kontenery
	docker compose up -d

down: ## Zatrzymaj kontenery
	docker compose down

restart: ## Restart kontenerów
	docker compose restart

logs: ## Logi wszystkich kontenerów
	docker compose logs -f

logs-app: ## Logi tylko app
	docker compose logs -f app

shell: ## Shell w kontenerze app
	docker compose exec app sh

mysql: ## MySQL CLI
	docker compose exec mysql mysql -u root -p

wayfinder: ## Wygeneruj Wayfinder routes ręcznie
	docker compose exec app php artisan wayfinder:generate --with-form
	@echo "✅ Wayfinder routes wygenerowane!"

optimize: ## Optymalizuj Laravel (cache config, routes, views)
	docker compose exec app php artisan config:cache
	docker compose exec app php artisan route:cache
	docker compose exec app php artisan view:cache
	docker compose exec app php artisan optimize
	@echo "✅ Optymalizacja zakończona!"

clean: ## Wyczyść wszystko (kontenery + volumes + node_modules)
	docker compose down -v
	rm -rf node_modules vendor public/build
	@echo "✅ Wyczyszczono!"

fresh: ## Fresh install (UWAGA: czyści bazę!)
	docker compose exec app php artisan migrate:fresh --seed

migrate: ## Uruchom migracje bazy danych
	docker compose exec app php artisan migrate

migrate-fresh: ## Wyczyść bazę i uruchom migracje (UWAGA: usuwa dane!)
	docker compose exec app php artisan migrate:fresh

migrate-rollback: ## Cofnij ostatnią migrację
	docker compose exec app php artisan migrate:rollback

seed: ## Zaseed'uj bazę danych
	docker compose exec app php artisan db:seed

clean-cache: ## Wyczyść cache Laravel
	docker compose exec app php artisan cache:clear
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan route:clear
	docker compose exec app php artisan view:clear

clean-docker: ## Wyczyść nieużywane obrazy i kontenery Docker
	docker system prune -f

test: ## Uruchom testy PHPUnit/Pest
	docker compose exec app php artisan test

tinker: ## Uruchom Laravel Tinker
	docker compose exec app php artisan tinker

queue-work: ## Uruchom queue worker (w tle)
	docker compose exec -d app php artisan queue:work

queue-restart: ## Restart queue workers
	docker compose exec app php artisan queue:restart

status: ## Pokaż status kontenerów
	docker compose ps

stats: ## Pokaż użycie zasobów przez kontenery
	docker stats --no-stream

redis-cli: ## Wejdź do konsoli Redis
	docker compose exec redis redis-cli

composer-install: ## Zainstaluj Composer dependencies w kontenerze
	docker compose exec app composer install

composer-update: ## Aktualizuj Composer dependencies w kontenerze
	docker compose exec app composer update

backup-db: ## Backup bazy danych do pliku
	@mkdir -p backups
	docker compose exec mysql mysqldump -u root -p journal_db > backups/backup_$$(date +%Y%m%d_%H%M%S).sql
	@echo "Backup created in backups/ directory"

restore-db: ## Restore bazy danych z ostatniego backupu
	@if [ -z "$(file)" ]; then echo "Usage: make restore-db file=backups/backup_20240101.sql"; exit 1; fi
	docker compose exec -T mysql mysql -u root -p journal_db < $(file)
	@echo "Database restored from $(file)"
