# 📓 Multilingual Journal - Laravel + Inertia.js + Vue 3

Wielojęzyczna aplikacja dziennika zbudowana na Laravel 12, Inertia.js 2.0 i Vue 3 z TypeScript.

## 🚀 Technologie

- **Backend**: Laravel 12.0, PHP 8.2+
- **Frontend**: Vue 3, TypeScript, Inertia.js 2.0
- **Build**: Vite 7
- **Styling**: TailwindCSS 4
- **Database**: MySQL 8.0 (prod) / SQLite (dev)
- **Cache/Queue**: Redis, Database Queue
- **Auth**: Laravel Fortify

## 📋 Wymagania

- PHP 8.2+
- Composer
- Node.js 20.x
- npm 10.9+
- MySQL 8.0 (dla produkcji)
- Docker & Docker Compose (opcjonalnie)

---

## 🏠 Development Setup (Lokalnie)

### Instalacja bez Docker

```bash
# Clone repozytorium
git clone https://github.com/ShoeRiderr/multilanguar-journal-laravel.git
cd multilanguar-journal-laravel

# Instalacja zależności
composer install
npm install

# Konfiguracja .env
cp .env.example .env
php artisan key:generate

# Migracje (SQLite w dev)
touch database/database.sqlite
php artisan migrate

# Build assetów
npm run build

# Uruchomienie development server
composer run dev
# lub osobno:
# php artisan serve
# npm run dev (w innym terminalu)
```

Aplikacja będzie dostępna pod: `http://localhost:8000`

---

## 🐳 Docker Development

### Quick Start z Docker

```bash
# Clone repozytorium
git clone https://github.com/ShoeRiderr/multilanguar-journal-laravel.git
cd multilanguar-journal-laravel

# Skopiuj i skonfiguruj .env
cp .env.example .env

# Zbuduj i uruchom kontenery
make build
make up

# Lub bez Makefile:
docker compose build
docker compose up -d

# Wygeneruj klucz aplikacji
make key-generate
# lub: docker compose exec app php artisan key:generate

# Uruchom migracje
make migrate
# lub: docker compose exec app php artisan migrate

# Zobacz logi
make logs
```

### Przydatne komendy Docker (Makefile)

```bash
make help          # Pokaż wszystkie dostępne komendy
make build         # Zbuduj kontenery
make up            # Uruchom kontenery
make down          # Zatrzymaj kontenery
make restart       # Restart kontenerów
make logs          # Wyświetl logi
make shell         # Bash w kontenerze app
make mysql         # MySQL shell
make redis         # Redis CLI
make test          # Uruchom testy
make optimize      # Zoptymalizuj (cache)
make clear         # Wyczyść cache
make fresh         # Fresh install (rebuild + migrate)
```

### Struktura Docker

Projekt używa multi-stage Dockerfile z następującymi serwisami:

- **app** (PHP 8.2-FPM): Aplikacja Laravel
- **nginx** (Alpine): Web server
- **mysql** (8.0): Baza danych
- **redis** (Alpine): Cache i session storage

---

## 🧪 Testowanie

```bash
# Lokalnie
php artisan test
composer run test

# W Docker
make test
# lub
docker compose exec app php artisan test
```

## 🎨 Linting i Formatowanie

```bash
# PHP (Pint)
composer run lint

# JavaScript/TypeScript (ESLint)
npm run lint

# Prettier
npm run format
```

---

## 🚀 Production Deployment

### DigitalOcean Droplet

Aplikacja jest gotowa do deploymentu na DigitalOcean z Docker Compose.

#### Quick Deployment

```bash
# Na serwerze DigitalOcean
git clone https://github.com/ShoeRiderr/multilanguar-journal-laravel.git /var/www/journal
cd /var/www/journal

# Konfiguracja
cp .env.production.example .env
nano .env  # Edytuj wartości produkcyjne

# Deploy
./deploy.sh
```

#### Automatyczny Deployment (GitHub Actions)

Projekt zawiera GitHub Actions workflow dla automatycznego deploymentu:

1. **Skonfiguruj GitHub Secrets**:
   - `SSH_PRIVATE_KEY`: Klucz SSH do dropletu
   - `DROPLET_HOST`: IP lub domena dropletu
   - `DROPLET_USER`: Użytkownik SSH (root/deploy)

2. **Push na main branch**:
   ```bash
   git push origin main
   ```

3. Workflow automatycznie:
   - Zbuduje i przetestuje aplikację
   - Wykona deployment na DigitalOcean
   - Uruchomi migracje i optymalizacje

### 📖 Pełna Dokumentacja Deployment

Szczegółowy przewodnik po deployment znajdziesz w:

**[docs/DEPLOYMENT.md](docs/DEPLOYMENT.md)**

Zawiera:
- Setup DigitalOcean Droplet
- Instalacja Docker
- Konfiguracja domeny i SSL
- GitHub Actions setup
- Backup i monitoring
- Troubleshooting

---

## 📁 Struktura Projektu

```
.
├── app/                    # Logika aplikacji Laravel
├── bootstrap/              # Bootstrap aplikacji
├── config/                 # Pliki konfiguracyjne
├── database/               # Migracje, seeders
├── docker/                 # Konfiguracja Docker
│   ├── nginx/             # Nginx config
│   └── php/               # PHP config
├── docs/                   # Dokumentacja
│   └── DEPLOYMENT.md      # Przewodnik deployment
├── public/                 # Publiczne pliki (index.php, assets)
├── resources/              # Views, Vue components, CSS
│   ├── css/
│   └── js/
│       ├── Components/    # Vue komponenty
│       ├── Layouts/       # Layouty Inertia
│       └── Pages/         # Strony Inertia
├── routes/                 # Routing
├── storage/                # Logi, cache, uploads
├── tests/                  # Testy (Pest)
├── .env.example            # Przykładowa konfiguracja (dev)
├── .env.production.example # Przykładowa konfiguracja (prod)
├── .env.testing            # Konfiguracja dla testów
├── Dockerfile              # Multi-stage Docker build
├── docker-compose.yml      # Docker Compose config
├── Makefile                # Skróty Docker Compose
├── deploy.sh               # Skrypt deploymentu
└── README.md               # Ten plik
```

---

## 🔧 Konfiguracja

### Environment Variables

Najważniejsze zmienne `.env`:

```env
# Aplikacja
APP_NAME="Multilingual Journal"
APP_ENV=local|production
APP_DEBUG=true|false
APP_URL=http://localhost

# Baza danych
DB_CONNECTION=sqlite|mysql
DB_HOST=mysql              # "mysql" w Docker
DB_DATABASE=journal_db
DB_USERNAME=journal_user
DB_PASSWORD=your_password

# Cache i Session
CACHE_STORE=database|redis
SESSION_DRIVER=database|redis
QUEUE_CONNECTION=database

# Redis (opcjonalnie)
REDIS_HOST=redis           # "redis" w Docker
REDIS_PORT=6379
```

---

## 🛠️ Development

### Uruchamianie development server

```bash
# Opcja 1: Wszystko w jednym (z kolejkami i logami)
composer run dev

# Opcja 2: Osobne terminale
php artisan serve          # Terminal 1
npm run dev               # Terminal 2
php artisan queue:work    # Terminal 3 (opcjonalnie)
```

### Hot Module Replacement (HMR)

Vite automatycznie odświeża przeglądarkę podczas zmian w:
- Vue components (`.vue`)
- TypeScript (`.ts`)
- CSS

### Database

```bash
# Migracje
php artisan migrate

# Rollback
php artisan migrate:rollback

# Fresh (reset + seed)
php artisan migrate:fresh --seed

# W Docker
make migrate
docker compose exec app php artisan migrate:fresh --seed
```

---

## 🤝 Contributing

1. Fork repozytorium
2. Utwórz feature branch (`git checkout -b feature/amazing-feature`)
3. Commit zmiany (`git commit -m 'Add amazing feature'`)
4. Push do brancha (`git push origin feature/amazing-feature`)
5. Otwórz Pull Request

### Code Style

Projekt używa:
- **PHP**: Laravel Pint (oparte na PHP CS Fixer)
- **JavaScript/TypeScript**: ESLint + Prettier

```bash
# Przed commitem uruchom:
composer run lint
npm run format
npm run lint
```

---

## 📝 License

Ten projekt jest licencjonowany na licencji MIT.

---

## 🙋 Support

Jeśli masz pytania lub napotkasz problemy:

1. Sprawdź [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md)
2. Przejrzyj [Issues](https://github.com/ShoeRiderr/multilanguar-journal-laravel/issues)
3. Utwórz nowy Issue

---

## 🎯 Features

- ✅ Wielojęzyczna obsługa (i18n)
- ✅ Autentykacja (Laravel Fortify)
- ✅ Inertia.js SPA experience
- ✅ Vue 3 Composition API
- ✅ TypeScript support
- ✅ TailwindCSS 4
- ✅ Rich text editor (TipTap)
- ✅ Docker ready
- ✅ CI/CD (GitHub Actions)
- ✅ Production ready

---

**Made with ❤️ using Laravel, Vue & Inertia**
