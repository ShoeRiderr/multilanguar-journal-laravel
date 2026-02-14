# Multilingual Journal

Tutaj zobaczysz stronę publicznie: https://codeitafterme.com

Aplikacja do prowadzenia wielojęzycznego dziennika online zbudowana z wykorzystaniem nowoczesnego stosu technologicznego.

## 🚀 Quick Start

### Lokalne uruchomienie (Development z hot reload)

```bash
# 1. Clone repo
git clone https://github.com/ShoeRiderr/multilanguar-journal-laravel.git
cd multilanguar-journal-laravel

# 2. Skopiuj .env
cp .env.example .env

# 3. Uruchom backend (Docker)
docker compose up -d app mysql redis

# 4. Setup Laravel
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate

# 5. Uruchom Vite dev server (hot reload)
npm install
npm run dev
```

Aplikacja: http://localhost  
Vite HMR: http://localhost:5173

### Deployment na Hetzner Cloud

**Wymagania na serwerze:**
- Docker + Docker Compose
- Node.js 20+ (do buildu assetów)

```bash
# Na serwerze:
git clone https://github.com/ShoeRiderr/multilanguar-journal-laravel.git
cd multilanguar-journal-laravel

# Setup
make install     # Kopiuje .env.example -> .env
nano .env        # Edytuj konfigurację

# Deploy
make deploy      # Wszystko automatycznie!
```

Pełna dokumentacja: [docs/HETZNER_DEPLOYMENT.md](docs/HETZNER_DEPLOYMENT.md)

## 🏗️ Architektura buildu

**Development:**
- Backend: Docker (app, mysql, redis)
- Frontend: Vite dev server (hot reload)
- Wayfinder: Działa lokalnie (npm run dev)

**Production build:**
1. Backend UP (docker compose up -d app mysql)
2. Build assetów NA HOŚCIE (npm run build)
   - Wayfinder używa: `docker compose exec app php artisan`
3. Rebuild image (kopiuje public/build)
4. Deploy (docker compose up -d)

### Alternatywnie: Używając Makefile

```bash
# Development mode
make dev         # Uruchom backend + frontend z hot reload

# Wyświetl wszystkie dostępne komendy
make help
```

## 📦 Stack technologiczny

### Backend
- **PHP**: 8.2
- **Laravel**: 12.0
- **Autentykacja**: Laravel Fortify
- **Baza danych**: MySQL 8.0 (produkcja) / SQLite (development)
- **Cache & Sessions**: Redis
- **Kolejki**: Database driver

### Frontend
- **Framework**: Vue 3
- **TypeScript**: 5.2+
- **Inertia.js**: 2.0
- **Build Tool**: Vite 7
- **Styling**: TailwindCSS 4
- **UI Components**: Reka UI, Lucide Icons
- **Rich Text**: TipTap Editor

### Infrastructure
- **Web Server**: Nginx (Alpine)
- **PHP-FPM**: 8.2 (Alpine)
- **Containerization**: Docker + Docker Compose
- **CI/CD**: GitHub Actions

## 🐳 Deployment na Hetzner Cloud

Aplikacja jest zoptymalizowana do uruchomienia na serwerze **Hetzner Cloud CX23** za jedyne ~€5.74/miesiąc (~$6.20/miesiąc)!

### Specyfikacja serwera CX23
- **CPU**: 2 vCPU shared
- **RAM**: 8GB
- **Storage**: 40GB SSD
- **Transfer**: 20TB/miesiąc
- **Koszt**: ~€5.74/miesiąc

### Pełna dokumentacja deploymentu

📖 **[docs/HETZNER_DEPLOYMENT.md](docs/HETZNER_DEPLOYMENT.md)** - Kompletny przewodnik krok po kroku

**Szybki start:**
1. Stwórz serwer CX23 na Hetzner Cloud
2. Zainstaluj Docker
3. Clone repo i skonfiguruj `.env`
4. Uruchom `docker compose up -d`
5. Skonfiguruj domenę + SSL (Let's Encrypt)
6. Setup GitHub Actions dla automatycznego deploymentu

### Automatyczny deployment

Po skonfigurowaniu GitHub Actions, każdy push do branch `main` automatycznie:
- Buduje i testuje aplikację
- Deployuje na serwer Hetzner
- Uruchamia migracje
- Optymalizuje cache
- Restartuje queue workers

## 🛠 Przydatne komendy

### Używając Makefile (zalecane)

```bash
make help        # Lista wszystkich komend
make dev         # Development mode (backend + frontend)
make build       # Zbuduj assety (wymaga działającego backendu)
make deploy      # Deploy na produkcję (full flow)
make up          # Uruchom kontenery
make down        # Zatrzymaj kontenery
make logs        # Logi wszystkich kontenerów
make logs-app    # Logi tylko app
make shell       # Shell w kontenerze app
make mysql       # MySQL CLI
make wayfinder   # Regeneruj Wayfinder routes
make optimize    # Optymalizuj Laravel (cache)
make migrate     # Uruchom migracje
make test        # Uruchom testy
make clean       # Wyczyść wszystko (kontenery + volumes)
make status      # Pokaż status kontenerów
make backup-db   # Backup bazy danych
```

### Docker Compose

```bash
# Uruchom kontenery
docker compose up -d

# Zatrzymaj kontenery
docker compose down

# Sprawdź status
docker compose ps

# Logi
docker compose logs -f

# Wejdź do kontenera
docker compose exec app bash

# Uruchom Artisan commands
docker compose exec app php artisan [command]
```

### Laravel Artisan

```bash
# Migracje
docker compose exec app php artisan migrate
docker compose exec app php artisan migrate:rollback
docker compose exec app php artisan migrate:fresh --seed

# Cache
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

# Kolejki
docker compose exec app php artisan queue:work
docker compose exec app php artisan queue:restart

# Tinker (REPL)
docker compose exec app php artisan tinker
```

## 🧪 Testing

```bash
# Uruchom wszystkie testy
make test

# Lub bezpośrednio:
docker compose exec app php artisan test

# Testy z coverage (jeśli skonfigurowane)
docker compose exec app php artisan test --coverage
```

## 📁 Struktura projektu

```
.
├── app/                    # Aplikacja Laravel (Models, Controllers, etc.)
├── bootstrap/              # Bootstrap Laravel
├── config/                 # Pliki konfiguracyjne
├── database/              # Migracje, seeders, factories
├── docker/                # Konfiguracje Docker
│   ├── nginx/            # Konfiguracja Nginx
│   ├── mysql/            # Optymalizacje MySQL
│   └── php/              # Konfiguracja PHP
├── docs/                  # Dokumentacja
│   └── HETZNER_DEPLOYMENT.md  # Deployment guide
├── public/                # Public assets, entry point
├── resources/             # Views, frontend code (Vue + TypeScript)
├── routes/                # Route definitions
├── storage/               # Logs, cache, uploads
├── tests/                 # Testy PHPUnit/Pest
├── .github/workflows/     # GitHub Actions CI/CD
├── docker-compose.yml     # Docker Compose configuration
├── Dockerfile             # Multi-stage Docker build
├── Makefile              # Shortcuts dla komend
├── deploy.sh             # Skrypt do ręcznego deploymentu
└── .env.production.example  # Przykładowa konfiguracja produkcyjna
```

## 🔧 Development

### Lokalne uruchomienie bez Docker

```bash
# Zainstaluj dependencies
composer install
npm install

# Przygotuj środowisko
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate

# Uruchom dev servers
composer dev  # Laravel + Queue + Pail + Vite (wszystko naraz)

# Lub oddzielnie:
php artisan serve        # Laravel dev server
npm run dev             # Vite dev server
php artisan queue:work  # Queue worker
```

### Linting i formatowanie

```bash
# PHP (Laravel Pint)
composer lint              # Automatyczna naprawa
composer test:lint         # Tylko sprawdzenie

# JavaScript/TypeScript (ESLint + Prettier)
npm run lint              # ESLint
npm run format            # Prettier fix
npm run format:check      # Prettier check
```

## 🔐 Bezpieczeństwo

- Wszystkie hasła jako zmienne środowiskowe
- MySQL nie exposed publicznie (tylko docker network)
- Security headers w Nginx (X-Frame-Options, X-Content-Type-Options, etc.)
- SSL/TLS z Let's Encrypt (darmowe certyfikaty)
- Firewall configuration (Hetzner + UFW)
- SSH key-based authentication (brak password auth)
- Fail2ban dla ochrony przed brute-force

## 📊 Optymalizacje

Aplikacja jest zoptymalizowana dla małego serwera (8GB RAM):
- **MySQL**: max 3GB RAM (innodb_buffer_pool_size=2G)
- **PHP-FPM**: max 2GB RAM
- **Nginx**: max 512MB RAM
- **Redis**: max 512MB RAM
- **OPcache**: włączony i skonfigurowany (256MB)
- **Realpath cache**: zwiększony (4MB)
- **Gzip compression**: poziom 6

## 🤝 Contributing

1. Fork repozytorium
2. Utwórz branch z feature (`git checkout -b feature/AmazingFeature`)
3. Commit zmian (`git commit -m 'Add some AmazingFeature'`)
4. Push do brancha (`git push origin feature/AmazingFeature`)
5. Otwórz Pull Request

## 📝 License

Ten projekt jest otwarty jako open-source. [MIT License](LICENSE)

## 🙋 Support

Masz pytania lub problemy? 
- Otwórz [Issue](https://github.com/ShoeRiderr/multilanguar-journal-laravel/issues)
- Sprawdź [dokumentację deploymentu](docs/HETZNER_DEPLOYMENT.md)

## 🎉 Credits

Zbudowane z ❤️ używając Laravel, Vue, Inertia.js i Docker.

---

**Koszty hostingu:** ~€5.74/miesiąc za kompletny serwer produkcyjny! 🚀
