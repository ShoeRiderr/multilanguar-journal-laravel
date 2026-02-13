# Docker Quick Start Guide

Szybki przewodnik dla projektu Multilingual Journal.

## 🚀 Pierwsze uruchomienie

```bash
# 1. Skopiuj .env
cp .env.example .env

# 2. Uruchom kontenery
docker compose up -d

# 3. Wygeneruj klucz aplikacji
docker compose exec app php artisan key:generate

# 4. Uruchom migracje
docker compose exec app php artisan migrate

# Gotowe! Aplikacja dostępna na http://localhost
```

## 📦 Używanie Makefile

```bash
make install    # Pełna instalacja (build + up + migrate)
make help       # Wszystkie dostępne komendy
make logs       # Zobacz logi
make shell      # Wejdź do kontenera
make test       # Uruchom testy
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

- `Dockerfile` - Multi-stage build aplikacji
- `docker-compose.yml` - Definicja serwisów
- `docker/nginx/` - Konfiguracja Nginx
- `docker/mysql/` - Optymalizacje MySQL  
- `docker/php/` - Konfiguracja PHP
- `deploy.sh` - Skrypt deploymentu
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
