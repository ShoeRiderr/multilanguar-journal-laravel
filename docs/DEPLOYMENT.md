# 🚀 Dokumentacja Deployment - Laravel + Docker + DigitalOcean

Kompletny przewodnik po deployment aplikacji Laravel 12 + Inertia.js + Vue 3 na DigitalOcean Droplet z użyciem Docker.

---

## 📋 Spis treści

1. [Wymagania](#wymagania)
2. [Setup DigitalOcean Droplet](#setup-digitalocean-droplet)
3. [Pierwszy Deployment](#pierwszy-deployment)
4. [Konfiguracja Domeny i SSL](#konfiguracja-domeny-i-ssl)
5. [GitHub Actions Setup](#github-actions-setup)
6. [Maintenance i Monitoring](#maintenance-i-monitoring)
7. [Troubleshooting](#troubleshooting)

---

## Wymagania

### Technologie
- **Docker**: 24.0+
- **Docker Compose**: 2.x
- **PHP**: 8.2
- **Laravel**: 12.0
- **Node.js**: 20.x
- **MySQL**: 8.0
- **Nginx**: Alpine
- **Redis**: Alpine

### Konto DigitalOcean
- Droplet Ubuntu 22.04
- Minimum 2GB RAM ($12/miesiąc)
- 50GB SSD
- Rekomendowane: 4GB RAM dla lepszej wydajności

---

## Setup DigitalOcean Droplet

### Krok 1: Utworzenie Droplet

1. Zaloguj się do [DigitalOcean](https://cloud.digitalocean.com/)
2. Kliknij **Create** → **Droplets**
3. Wybierz:
   - **Distribution**: Ubuntu 22.04 LTS
   - **Plan**: Basic
   - **CPU Options**: Regular (2GB RAM / 2 vCPU / 50GB SSD)
   - **Datacenter**: Najbliższy Twojej lokalizacji
   - **Authentication**: SSH keys (zalecane) lub Password
   - **Hostname**: `journal-production` (lub własna nazwa)

4. Kliknij **Create Droplet**

### Krok 2: Pierwsze połączenie SSH

```bash
# Połącz się z droplet (użyj swojego IP)
ssh root@YOUR_DROPLET_IP

# Zaktualizuj system
apt update && apt upgrade -y
```

### Krok 3: Instalacja Docker

```bash
# Zainstaluj Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh

# Uruchom Docker
systemctl start docker
systemctl enable docker

# Sprawdź wersję
docker --version
```

### Krok 4: Instalacja Docker Compose

```bash
# Docker Compose jest teraz częścią Docker CLI
# Sprawdź instalację
docker compose version

# Powinno wyświetlić: Docker Compose version v2.x.x
```

### Krok 5: Konfiguracja Firewall (UFW)

```bash
# Włącz firewall
ufw --force enable

# Zezwól na SSH (WAŻNE! Najpierw SSH!)
ufw allow 22/tcp

# Zezwól na HTTP i HTTPS
ufw allow 80/tcp
ufw allow 443/tcp

# Sprawdź status
ufw status

# Powinno pokazać:
# Status: active
# To                         Action      From
# --                         ------      ----
# 22/tcp                     ALLOW       Anywhere
# 80/tcp                     ALLOW       Anywhere
# 443/tcp                    ALLOW       Anywhere
```

### Krok 6: Utworzenie użytkownika deploy (opcjonalne, zalecane)

```bash
# Utwórz użytkownika
adduser deploy

# Dodaj do grupy sudo
usermod -aG sudo deploy

# Dodaj do grupy docker
usermod -aG docker deploy

# Przełącz się na użytkownika deploy
su - deploy
```

### Krok 7: Setup SSH Keys

```bash
# Na lokalnym komputerze (jeśli nie masz klucza)
ssh-keygen -t ed25519 -C "deploy@journal"

# Skopiuj klucz publiczny na serwer
ssh-copy-id deploy@YOUR_DROPLET_IP

# Lub ręcznie:
# 1. Wyświetl klucz: cat ~/.ssh/id_ed25519.pub
# 2. Na serwerze: mkdir -p ~/.ssh && nano ~/.ssh/authorized_keys
# 3. Wklej klucz i zapisz
```

---

## Pierwszy Deployment

### Krok 1: Clone repozytorium

```bash
# Zaloguj się jako deploy (lub root)
ssh deploy@YOUR_DROPLET_IP

# Utwórz katalog dla aplikacji
mkdir -p /var/www/journal
cd /var/www/journal

# Clone repozytorium
git clone https://github.com/YOUR_USERNAME/multilanguar-journal-laravel.git .

# Lub jeśli repo jest prywatne, skonfiguruj SSH key dla GitHuba
ssh-keygen -t ed25519 -C "server@journal"
cat ~/.ssh/id_ed25519.pub
# Dodaj ten klucz do GitHub Deploy Keys w ustawieniach repo
```

### Krok 2: Konfiguracja .env

```bash
# Skopiuj przykładową konfigurację produkcyjną
cp .env.production.example .env

# Edytuj .env
nano .env
```

**Ważne ustawienia do zmiany:**

```env
APP_NAME="Multilingual Journal"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Baza danych
DB_DATABASE=journal_db
DB_USERNAME=journal_user
DB_PASSWORD=ZMIEŃ_NA_SILNE_HASŁO
DB_ROOT_PASSWORD=ZMIEŃ_NA_INNE_SILNE_HASŁO

# Mail (skonfiguruj swojego dostawcę)
MAIL_MAILER=smtp
MAIL_HOST=smtp.twoj-dostawca.com
MAIL_PORT=587
MAIL_USERNAME=twoj-email@example.com
MAIL_PASSWORD=twoje-haslo
MAIL_FROM_ADDRESS=noreply@your-domain.com
```

### Krok 3: Wygeneruj APP_KEY

```bash
# Zbuduj tymczasowy kontener do generowania klucza
docker compose run --rm app php artisan key:generate

# Lub ręcznie wygeneruj i dodaj do .env:
# APP_KEY=base64:TUTAJ_WYGENEROWANY_KLUCZ
```

### Krok 4: Build i uruchomienie kontenerów

```bash
# Zbuduj kontenery
docker compose build

# Uruchom w tle
docker compose up -d

# Sprawdź status
docker compose ps

# Wszystkie powinny być "Up"
```

### Krok 5: Uruchom migracje

```bash
# Poczekaj 10 sekund na uruchomienie MySQL
sleep 10

# Uruchom migracje
docker compose exec app php artisan migrate --force

# Jeśli masz seeders:
# docker compose exec app php artisan db:seed --force
```

### Krok 6: Optymalizacja

```bash
# Cache konfiguracji
docker compose exec app php artisan config:cache

# Cache routes
docker compose exec app php artisan route:cache

# Cache views
docker compose exec app php artisan view:cache

# Optymalizacja
docker compose exec app php artisan optimize
```

### Krok 7: Sprawdź czy działa

```bash
# Sprawdź logi
docker compose logs -f

# W przeglądarce otwórz:
http://YOUR_DROPLET_IP
```

---

## Konfiguracja Domeny i SSL

### Krok 1: Konfiguracja DNS

W panelu Twojego dostawcy domeny (np. Cloudflare, Namecheap):

1. Dodaj **A Record**:
   - Type: `A`
   - Name: `@` (lub subdomena np. `journal`)
   - Value: `YOUR_DROPLET_IP`
   - TTL: `Auto` lub `300`

2. Dodaj **CNAME Record** dla www (opcjonalnie):
   - Type: `CNAME`
   - Name: `www`
   - Value: `your-domain.com`
   - TTL: `Auto` or `300`

3. Poczekaj na propagację DNS (może zająć do 24h, zazwyczaj 5-30 min)

Sprawdź propagację: `dig your-domain.com +short`

### Krok 2: Aktualizacja Nginx config

```bash
# Edytuj konfigurację Nginx
nano docker/nginx/default.conf
```

Zmień `server_name`:
```nginx
server_name your-domain.com www.your-domain.com;
```

### Krok 3: Instalacja Certbot (Let's Encrypt SSL)

#### Opcja A: Certbot w kontenerze Nginx (zalecane)

Zaktualizuj `docker-compose.yml`:

```yaml
nginx:
  image: nginx:alpine
  # ... reszta konfiguracji ...
  volumes:
    - ./:/var/www/html
    - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    - ./docker/nginx/ssl:/etc/nginx/ssl  # Dodaj SSL certs
    - ./docker/certbot/conf:/etc/letsencrypt  # Certbot config
    - ./docker/certbot/www:/var/www/certbot  # ACME challenge
```

Dodaj serwis Certbot:

```yaml
certbot:
  image: certbot/certbot
  container_name: journal_certbot
  volumes:
    - ./docker/certbot/conf:/etc/letsencrypt
    - ./docker/certbot/www:/var/www/certbot
  entrypoint: "/bin/sh -c 'trap exit TERM; while :; do certbot renew; sleep 12h & wait $${!}; done;'"
```

Zaktualizuj Nginx config aby obsługiwał ACME challenge:

```nginx
# Dodaj przed główną lokacją /
location ~ /.well-known/acme-challenge {
    allow all;
    root /var/www/certbot;
}
```

Wygeneruj certyfikat:

```bash
# Restart Nginx z nową konfiguracją
docker compose restart nginx

# Wygeneruj certyfikat
docker compose run --rm certbot certonly \
  --webroot \
  --webroot-path=/var/www/certbot \
  --email your-email@example.com \
  --agree-tos \
  --no-eff-email \
  -d your-domain.com \
  -d www.your-domain.com
```

#### Opcja B: Certbot bezpośrednio na serwerze

```bash
# Zainstaluj Certbot
apt install certbot python3-certbot-nginx -y

# Zatrzymaj Nginx (tymczasowo)
docker compose stop nginx

# Wygeneruj certyfikat (standalone mode)
certbot certonly --standalone \
  --preferred-challenges http \
  --email your-email@example.com \
  --agree-tos \
  -d your-domain.com \
  -d www.your-domain.com

# Uruchom Nginx
docker compose start nginx
```

### Krok 4: Konfiguracja HTTPS w Nginx

Zaktualizuj `docker/nginx/default.conf`:

```nginx
# HTTP - redirect na HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    
    # ACME challenge
    location ~ /.well-known/acme-challenge {
        allow all;
        root /var/www/certbot;
    }
    
    # Redirect wszystko inne na HTTPS
    location / {
        return 301 https://$server_name$request_uri;
    }
}

# HTTPS
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name your-domain.com www.your-domain.com;
    
    # SSL Certificates
    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;
    
    # SSL Configuration
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    
    # ... reszta konfiguracji (root, locations, itp.)
}
```

Restart Nginx:

```bash
docker compose restart nginx
```

### Krok 5: Auto-renewal SSL

Certbot automatycznie dodaje cron job do odnowienia certyfikatów. Sprawdź:

```bash
# Test odnowienia
certbot renew --dry-run

# Jeśli używasz kontenera:
docker compose run --rm certbot renew --dry-run
```

Dodaj cron job (jeśli potrzebne):

```bash
crontab -e

# Dodaj linię (odnowienie o 3 AM codziennie):
0 3 * * * docker compose -f /var/www/journal/docker-compose.yml run --rm certbot renew --quiet && docker compose -f /var/www/journal/docker-compose.yml restart nginx
```

---

## GitHub Actions Setup

### Krok 1: Generowanie SSH Key dla CI/CD

Na **lokalnym komputerze**:

```bash
# Wygeneruj nową parę kluczy specjalnie dla CI/CD
ssh-keygen -t ed25519 -C "github-actions@journal" -f ~/.ssh/github_actions_deploy

# Wyświetl klucz publiczny
cat ~/.ssh/github_actions_deploy.pub

# Wyświetl klucz prywatny (będzie potrzebny dla GitHub Secrets)
cat ~/.ssh/github_actions_deploy
```

### Krok 2: Dodanie klucza publicznego na serwerze

Na **serwerze DigitalOcean**:

```bash
# Zaloguj się jako deploy
ssh deploy@YOUR_DROPLET_IP

# Edytuj authorized_keys
nano ~/.ssh/authorized_keys

# Dodaj klucz publiczny z poprzedniego kroku na końcu pliku
# Zapisz i wyjdź (Ctrl+O, Enter, Ctrl+X)

# Ustaw odpowiednie uprawnienia
chmod 600 ~/.ssh/authorized_keys
chmod 700 ~/.ssh
```

### Krok 3: Test połączenia

Na **lokalnym komputerze**:

```bash
# Test połączenia z kluczem
ssh -i ~/.ssh/github_actions_deploy deploy@YOUR_DROPLET_IP

# Jeśli się łączy, wszystko działa!
```

### Krok 4: Konfiguracja GitHub Secrets

1. Idź do Twojego repozytorium na GitHubie
2. Kliknij **Settings** → **Secrets and variables** → **Actions**
3. Kliknij **New repository secret**

Dodaj następujące secrets:

**SSH_PRIVATE_KEY**:
```
Skopiuj CAŁĄ zawartość pliku ~/.ssh/github_actions_deploy
Włącznie z liniami:
-----BEGIN OPENSSH PRIVATE KEY-----
...
-----END OPENSSH PRIVATE KEY-----
```

**DROPLET_HOST**:
```
YOUR_DROPLET_IP
# lub domena: your-domain.com
```

**DROPLET_USER**:
```
deploy
# lub root (jeśli nie utworzyłeś użytkownika deploy)
```

### Krok 5: Przygotowanie serwera dla GitHub Actions

Na **serwerze**:

```bash
# Upewnij się, że katalog projektu istnieje
cd /var/www/journal

# Dodaj safe directory dla Git (jeśli potrzebne)
git config --global --add safe.directory /var/www/journal

# Upewnij się że użytkownik deploy ma uprawnienia
sudo chown -R deploy:deploy /var/www/journal
```

### Krok 6: Test workflow

1. Zrób jakąś zmianę w kodzie
2. Commit i push na branch `main`:
   ```bash
   git add .
   git commit -m "Test deployment workflow"
   git push origin main
   ```
3. Idź do **Actions** tab w GitHubie
4. Obserwuj workflow - powinien:
   - ✅ Build and Test
   - ✅ Deploy to Production

### Krok 7: Monitoring deploymentów

Na serwerze sprawdź logi:

```bash
# Logi z ostatniego deploymentu
docker compose logs -f

# Status kontenerów
docker compose ps

# Sprawdź aplikację
curl -I https://your-domain.com
```

---

## Maintenance i Monitoring

### Backup bazy danych

#### Automatyczny backup (cron)

Utwórz skrypt backup:

```bash
# Utwórz katalog dla backupów
mkdir -p /var/www/journal/backups

# Utwórz skrypt
nano /var/www/journal/backup.sh
```

Zawartość `backup.sh`:

```bash
#!/bin/bash

BACKUP_DIR="/var/www/journal/backups"
DATE=$(date +%Y%m%d_%H%M%S)
FILENAME="journal_backup_$DATE.sql.gz"

# Backup bazy danych
docker compose exec -T mysql mysqldump \
  -u root \
  -p${DB_ROOT_PASSWORD} \
  ${DB_DATABASE} | gzip > "$BACKUP_DIR/$FILENAME"

# Usuń backupy starsze niż 7 dni
find $BACKUP_DIR -name "journal_backup_*.sql.gz" -mtime +7 -delete

echo "Backup created: $FILENAME"
```

```bash
# Zrób skrypt wykonywalnym
chmod +x /var/www/journal/backup.sh

# Dodaj do cron
crontab -e

# Backup codziennie o 2 AM
0 2 * * * /var/www/journal/backup.sh >> /var/www/journal/backups/backup.log 2>&1
```

#### Ręczny backup

```bash
# Backup bazy danych
docker compose exec mysql mysqldump \
  -u root \
  -pYOUR_ROOT_PASSWORD \
  journal_db > backup_$(date +%Y%m%d).sql

# Kompresja
gzip backup_$(date +%Y%m%d).sql

# Kopiuj na lokalny komputer
scp deploy@YOUR_DROPLET_IP:/var/www/journal/backup_*.sql.gz ./
```

#### Restore z backupu

```bash
# Skopiuj backup na serwer
scp backup_20240101.sql.gz deploy@YOUR_DROPLET_IP:/tmp/

# Na serwerze
cd /var/www/journal
gunzip /tmp/backup_20240101.sql.gz

# Restore
docker compose exec -T mysql mysql \
  -u root \
  -pYOUR_ROOT_PASSWORD \
  journal_db < /tmp/backup_20240101.sql
```

### Monitoring Logów

```bash
# Wszystkie logi
docker compose logs -f

# Tylko app (Laravel)
docker compose logs -f app

# Tylko Nginx
docker compose logs -f nginx

# Ostatnie 100 linii
docker compose logs --tail=100 app

# Logi Laravel w storage
docker compose exec app tail -f storage/logs/laravel.log
```

### Monitoring zasobów

```bash
# Użycie CPU/RAM przez kontenery
docker stats

# Przestrzeń dyskowa
df -h

# Rozmiar volumes
docker system df
```

### Czyszczenie (clean up)

```bash
# Usuń nieużywane images
docker image prune -a

# Usuń nieużywane volumes
docker volume prune

# Czyszczenie Laravel cache
docker compose exec app php artisan cache:clear
docker compose exec app php artisan view:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
```

### Aktualizacje

```bash
# Pull najnowszego kodu
cd /var/www/journal
git pull origin main

# Rebuild i restart
docker compose down
docker compose build --no-cache
docker compose up -d

# Migracje
docker compose exec app php artisan migrate --force

# Optymalizacja
docker compose exec app php artisan optimize
```

### Skalowanie

Jeśli aplikacja potrzebuje więcej zasobów:

1. **Vertical Scaling** (zwiększ rozmiar Dropletu):
   - W DigitalOcean dashboard: Resize droplet
   - Wybierz większy plan (np. 4GB RAM)
   - Restart automatyczny

2. **Horizontal Scaling** (więcej serwerów):
   - Dodaj Load Balancer w DigitalOcean
   - Utwórz więcej Dropletów z tą samą aplikacją
   - Skonfiguruj shared database (Managed MySQL)
   - Shared Redis dla cache/sessions

---

## Troubleshooting

### Problem: Kontenery nie startują

```bash
# Sprawdź logi
docker compose logs

# Sprawdź konkretny kontener
docker compose logs app
docker compose logs mysql

# Restart
docker compose restart

# Pełny rebuild
docker compose down
docker compose build --no-cache
docker compose up -d
```

### Problem: MySQL connection refused

```bash
# Sprawdź czy MySQL jest uruchomiony
docker compose ps mysql

# Sprawdź health check
docker compose exec mysql mysqladmin ping -h localhost -u root -p

# Sprawdź .env - DB_HOST musi być "mysql" (nie localhost)
grep DB_HOST .env

# Zrestartuj MySQL z czekaniem
docker compose restart mysql
sleep 10
docker compose exec app php artisan migrate
```

### Problem: 502 Bad Gateway (Nginx)

```bash
# Sprawdź czy PHP-FPM działa
docker compose ps app

# Sprawdź logi Nginx
docker compose logs nginx

# Sprawdź logi app
docker compose logs app

# Restart app
docker compose restart app
```

### Problem: Permission denied dla storage/

```bash
# Napraw uprawnienia
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
docker compose exec app chmod -R 775 storage bootstrap/cache
```

### Problem: Assets (CSS/JS) nie ładują się

```bash
# Sprawdź czy pliki są zbudowane
docker compose exec app ls -la public/build

# Jeśli brak, zbuduj assety
docker compose exec app npm install
docker compose exec app npm run build

# Sprawdź Vite manifest
docker compose exec app cat public/build/manifest.json
```

### Problem: GitHub Actions deployment fails

1. **SSH connection error**:
   - Sprawdź czy SSH_PRIVATE_KEY secret jest poprawny
   - Sprawdź czy klucz publiczny jest w ~/.ssh/authorized_keys na serwerze
   - Test: `ssh -i key.pem deploy@DROPLET_IP`

2. **Permission denied na serwerze**:
   ```bash
   # Upewnij się że deploy ma uprawnienia
   sudo chown -R deploy:deploy /var/www/journal
   sudo usermod -aG docker deploy
   ```

3. **Git pull fails**:
   ```bash
   # Safe directory
   git config --global --add safe.directory /var/www/journal
   ```

### Problem: SSL certyfikat wygasł

```bash
# Ręczne odnowienie
certbot renew

# Lub w kontenerze
docker compose run --rm certbot renew

# Restart Nginx
docker compose restart nginx

# Sprawdź datę wygaśnięcia
echo | openssl s_client -servername your-domain.com -connect your-domain.com:443 2>/dev/null | openssl x509 -noout -dates
```

---

## 📞 Kontakt i Wsparcie

W przypadku problemów:
1. Sprawdź sekcję [Troubleshooting](#troubleshooting)
2. Przejrzyj logi: `docker compose logs -f`
3. Sprawdź GitHub Issues w repozytorium
4. Dokumentacja Laravel: https://laravel.com/docs
5. Dokumentacja Docker: https://docs.docker.com/

---

## 📚 Dodatkowe zasoby

- [Laravel Deployment Documentation](https://laravel.com/docs/12.x/deployment)
- [DigitalOcean Community Tutorials](https://www.digitalocean.com/community/tutorials)
- [Docker Best Practices](https://docs.docker.com/develop/dev-best-practices/)
- [Let's Encrypt Documentation](https://letsencrypt.org/docs/)
- [Nginx Configuration Guide](https://nginx.org/en/docs/)

---

**Happy Deploying! 🚀**
