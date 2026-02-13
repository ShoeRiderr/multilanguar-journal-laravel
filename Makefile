.PHONY: help build up down restart logs shell mysql deploy optimize migrate fresh clean test

# Makefile dla projektu Laravel + Docker
# Użycie: make [command]

help: ## Pokaż tę pomoc
	@echo "Dostępne komendy:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

build: ## Zbuduj obrazy Docker
	docker compose build --no-cache

up: ## Uruchom kontenery w tle
	docker compose up -d

down: ## Zatrzymaj i usuń kontenery
	docker compose down

restart: ## Restartuj wszystkie kontenery
	docker compose restart

logs: ## Pokaż logi wszystkich kontenerów (Ctrl+C aby wyjść)
	docker compose logs -f

logs-app: ## Pokaż logi aplikacji PHP
	docker compose logs -f app

logs-nginx: ## Pokaż logi Nginx
	docker compose logs -f nginx

logs-mysql: ## Pokaż logi MySQL
	docker compose logs -f mysql

shell: ## Wejdź do kontenera app (bash)
	docker compose exec app bash

mysql: ## Wejdź do konsoli MySQL
	docker compose exec mysql mysql -u root -p

redis-cli: ## Wejdź do konsoli Redis
	docker compose exec redis redis-cli

deploy: ## Uruchom pełny deployment (pull, build, migrate, optimize)
	./deploy.sh

optimize: ## Optymalizuj Laravel (config, route, view cache)
	docker compose exec app php artisan optimize
	docker compose exec app php artisan config:cache
	docker compose exec app php artisan route:cache
	docker compose exec app php artisan view:cache

migrate: ## Uruchom migracje bazy danych
	docker compose exec app php artisan migrate

migrate-fresh: ## Wyczyść bazę i uruchom migracje (UWAGA: usuwa dane!)
	docker compose exec app php artisan migrate:fresh

migrate-rollback: ## Cofnij ostatnią migrację
	docker compose exec app php artisan migrate:rollback

seed: ## Zaseed'uj bazę danych
	docker compose exec app php artisan db:seed

fresh: ## Fresh install (UWAGA: czyści bazę danych!)
	docker compose exec app php artisan migrate:fresh --seed

clean: ## Wyczyść cache Laravel
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

install: ## Pierwsza instalacja (build, up, migrate)
	docker compose build
	docker compose up -d
	@echo "Waiting for MySQL to be ready..."
	@sleep 15
	docker compose exec app php artisan migrate --force
	docker compose exec app php artisan optimize
	@echo "Installation complete! Check http://localhost"

npm-install: ## Zainstaluj npm dependencies (lokalnie)
	npm install

npm-build: ## Zbuduj assety (lokalnie)
	npm run build

npm-dev: ## Uruchom Vite dev server (lokalnie)
	npm run dev

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
