# Docker Quick Start Guide

Szybki przewodnik dla projektu Multilingual Journal.

## 🚀 Pierwsze uruchomienie

### Opcja 1: Development mode (z Vite hot reload)

```bash
# 1. Skopiuj .env
cp .env.example .env

# 2. Uruchom w trybie dev
make dev

# 3. Setup aplikacji
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate

# Gotowe! 
# Aplikacja: http://localhost
# Vite dev: http://localhost:5173
```

### Opcja 2: Production mode

```bash
# 1. Skopiuj .env
cp .env.example .env

# 2. Zbuduj assety i uruchom
make build
make prod

# 3. Setup aplikacji
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate

# Gotowe! Aplikacja dostępna na http://localhost
```

## 📦 Używanie Makefile

```bash
make dev         # Development mode z hot reload
make prod        # Production mode
make build       # Zbuduj assety + Docker image
make assets      # Zbuduj tylko assety (npm)
make help        # Wszystkie dostępne komendy
make logs        # Zobacz logi
make shell       # Wejdź do kontenera
make test        # Uruchom testy
```

## ⚙️ Strategia budowania assetów

**WAŻNE:** Assety frontend (Vite build) są budowane **NA HOŚCIE**, nie w Dockerze.

### Dlaczego?

Plugin `@laravel/vite-plugin-wayfinder` wymaga PHP do generowania route types. W Docker multi-stage build Node stage nie ma PHP, więc build zawodzi.

### Rozwiązanie:

1. **Development:** Vite dev server w osobnym kontenerze (`make dev`)
2. **Production:** Build assetów na hoście przed dockerem (`make build` lub `npm run build`)

### Workflow produkcyjny:

```bash
# 1. Build assetów (na hoście, gdzie PHP jest dostępne)
npm ci
npm run build

# 2. Build Docker image (kopiuje gotowe assety)
docker compose build app

# 3. Uruchom
docker compose up -d

# 4. Wygeneruj Wayfinder routes (w kontenerze z PHP)
docker compose exec app php artisan wayfinder:generate --with-form
```

## 🛠 Podstawowe komendy

### Zarządzanie kontenerami
```bash
docker compose up -d        # Uruchom w tle
docker compose down         # Zatrzymaj
docker compose restart      # Restart
docker compose ps           # Status
docker compose logs -f      # Logi
```

### Laravel Artisan
```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan optimize
docker compose exec app php artisan tinker
```

### Baza danych
```bash
docker compose exec mysql mysql -u root -p
# Hasło: sprawdź DB_PASSWORD w .env
```

## 📁 Struktura

- `Dockerfile` - Uproszczony build aplikacji (bez Node)
- `Dockerfile.with-assets` - Opcjonalny build z assetami w Dockerze
- `docker-compose.yml` - Definicja serwisów (produkcja)
- `docker-compose.dev.yml` - Nadpisanie dla development (Vite dev)
- `docker/nginx/` - Konfiguracja Nginx
- `docker/mysql/` - Optymalizacje MySQL  
- `docker/php/` - Konfiguracja PHP
- `deploy.sh` - Skrypt deploymentu (buduje assety + Docker)
- `Makefile` - Skróty komend

## 🐳 Serwisy

- **app** (PHP-FPM 8.2) - port wewnętrzny 9000
- **nginx** - porty 80, 443
- **mysql** - port 3306 (tylko localhost)
- **redis** - port 6379 (wewnętrzny)

## 🔧 Development

Dla lokalnego developmentu:
```bash
cp docker-compose.override.yml.example docker-compose.override.yml
# Edytuj według potrzeb (np. expose MySQL port, Xdebug, etc.)
```

## 📖 Więcej informacji

- **README.md** - Pełna dokumentacja projektu
- **docs/HETZNER_DEPLOYMENT.md** - Deployment na produkcję
- **make help** - Lista wszystkich komend

## 🆘 Troubleshooting

### Problem: Vite build errors podczas Docker build

**Objawy:** `php: not found` lub błędy Wayfinder podczas `npm run build`

**Rozwiązanie:**
```bash
# Assety MUSZĄ być zbudowane NA HOŚCIE, nie w Dockerze
npm ci
npm run build

# Sprawdź czy build się udał
ls -la public/build/

# Potem uruchom Docker
docker compose up -d
```

### Port zajęty
```bash
# Sprawdź co używa portu 80
sudo lsof -i :80
# Zatrzymaj konfliktujący serwis lub zmień port w docker-compose.yml
```

### Kontener nie startuje
```bash
docker compose logs [service_name]
docker compose down
docker compose up -d
```

### Brak połączenia z bazą
```bash
# Sprawdź czy MySQL jest gotowy
docker compose exec mysql mysqladmin ping -h localhost

# Zaczekaj chwilę i spróbuj ponownie
sleep 10
docker compose exec app php artisan migrate
```

### Czyszczenie
```bash
make clean-docker    # Wyczyść Docker
make clean           # Wyczyść cache Laravel
```
