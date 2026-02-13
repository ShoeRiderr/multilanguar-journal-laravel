# Deployment na Hetzner Cloud CX23

Kompletny przewodnik krok po kroku do uruchomienia aplikacji Laravel 12 + Inertia.js + Vue 3 + TypeScript na serwerze Hetzner Cloud CX23.

## 📋 Spis treści

1. [Zakładanie serwera Hetzner Cloud](#część-1-zakładanie-serwera-hetzner-cloud)
2. [Pierwszy setup serwera](#część-2-pierwszy-setup-serwera)
3. [Konfiguracja aplikacji](#część-3-konfiguracja-aplikacji)
4. [Konfiguracja domeny + SSL](#część-4-konfiguracja-domeny--ssl-lets-encrypt)
5. [GitHub Actions Setup (CI/CD)](#część-5-github-actions-setup-cicd)
6. [Monitorowanie i maintenance](#część-6-monitorowanie-i-maintenance)
7. [Optymalizacje i bezpieczeństwo](#część-7-optymalizacje-i-bezpieczeństwo)

## 💰 Koszty

- **Serwer Hetzner CX23**: ~€5.74/mies (~$6.20/mies)
- **Domena** (opcjonalnie): ~€10-15/rok
- **SSL**: Darmowy (Let's Encrypt)
- **Backup** (opcjonalnie): +20% (€1.15/mies)

**Razem**: ~€5.74/mies za kompletny serwer produkcyjny!

## 📊 Specyfikacja serwera CX23

- **CPU**: 2 vCPU shared
- **RAM**: 8GB
- **Storage**: 40GB SSD
- **Network**: 20TB transfer
- **Location**: Nuremberg, Helsinki, lub Falkenstein
- **OS**: Ubuntu 22.04 LTS

---

## Część 1: Zakładanie serwera Hetzner Cloud

### 1.1. Utworzenie konta

1. Przejdź na https://www.hetzner.com/cloud
2. Utwórz konto (możesz otrzymać €20 kredytu startowego)
3. Zweryfikuj email

### 1.2. Utworzenie projektu

1. Zaloguj się do panelu Cloud Console
2. Kliknij **"New Project"**
3. Nazwij projekt (np. "Multilingual Journal")

### 1.3. Utworzenie serwera

1. W projekcie kliknij **"Add Server"**
2. Wybierz konfigurację:
   - **Location**: Nuremberg (najbliżej Polski) lub Helsinki
   - **Image**: Ubuntu 22.04
   - **Type**: **CX23** (2 vCPU, 8GB RAM, 40GB SSD)
   - **Volume**: Nie potrzebny na początku
   - **Network**: Domyślnie (Public IPv4 + IPv6)

3. **SSH Key** (WAŻNE!):
   ```bash
   # Jeśli nie masz klucza SSH, wygeneruj go lokalnie:
   ssh-keygen -t ed25519 -C "your-email@example.com"
   
   # Wyświetl publiczny klucz:
   cat ~/.ssh/id_ed25519.pub
   ```
   
   Skopiuj zawartość i dodaj jako nowy klucz SSH w panelu Hetzner

4. **Firewall** (zalecane):
   - Kliknij "Create Firewall"
   - Nazwa: "web-firewall"
   - **Inbound Rules**:
     - SSH (TCP/22) - Source: 0.0.0.0/0, ::/0
     - HTTP (TCP/80) - Source: 0.0.0.0/0, ::/0
     - HTTPS (TCP/443) - Source: 0.0.0.0/0, ::/0
   - **Outbound Rules**: Allow all

5. Kliknij **"Create & Buy now"**

6. **Zapisz IP serwera** (np. 123.45.67.89)

---

## Część 2: Pierwszy setup serwera

### 2.1. Połączenie z serwerem

```bash
ssh root@YOUR_SERVER_IP
```

Przy pierwszym logowaniu potwierdź fingerprint serwera.

### 2.2. Aktualizacja systemu

```bash
# Aktualizuj listy pakietów
apt update

# Zainstaluj wszystkie aktualizacje
apt upgrade -y

# Zainstaluj podstawowe narzędzia
apt install -y curl wget vim git unzip
```

### 2.3. Instalacja Docker

```bash
# Pobierz i uruchom oficjalny skrypt instalacyjny Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh

# Usuń skrypt
rm get-docker.sh

# Sprawdź instalację
docker --version
```

### 2.4. Instalacja Docker Compose

```bash
# Docker Compose Plugin jest już zainstalowany z Docker
# Sprawdź wersję
docker compose version
```

### 2.5. Utworzenie użytkownika deploy (opcjonalnie, ale zalecane)

```bash
# Utwórz użytkownika
adduser deploy

# Dodaj do grupy sudo
usermod -aG sudo deploy

# Dodaj do grupy docker (aby mógł używać Docker bez sudo)
usermod -aG docker deploy

# Skopiuj klucze SSH do nowego użytkownika
rsync --archive --chown=deploy:deploy ~/.ssh /home/deploy/

# Przełącz się na nowego użytkownika
su - deploy
```

### 2.6. Clone repozytorium

```bash
# Przejdź do katalogu domowego
cd ~

# Clone repozytorium (użyj HTTPS lub SSH)
git clone https://github.com/ShoeRiderr/multilanguar-journal-laravel.git

# Przejdź do katalogu projektu
cd multilanguar-journal-laravel

# Sprawdź czy wszystkie pliki są obecne
ls -la
```

---

## Część 3: Konfiguracja aplikacji

### 3.1. Konfiguracja zmiennych środowiskowych

```bash
# Skopiuj przykładowy plik produkcyjny
cp .env.production.example .env

# Edytuj plik .env
nano .env
```

**Ustaw następujące zmienne:**

```env
APP_NAME="Multilingual Journal"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com  # Zmień na swoją domenę

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=journal_db
DB_USERNAME=journal_user
DB_PASSWORD=STRONG_RANDOM_PASSWORD_HERE  # Wygeneruj silne hasło!

CACHE_STORE=redis
SESSION_DRIVER=redis
REDIS_HOST=redis
```

**Wygeneruj silne hasło dla bazy danych:**
```bash
openssl rand -base64 32
```

Zapisz plik: `Ctrl+X`, `Y`, `Enter`

### 3.2. Wygenerowanie klucza aplikacji

```bash
# Uruchom tymczasowy kontener do wygenerowania klucza
docker compose run --rm app php artisan key:generate
```

### 3.3. Utworzenie samopodpisanego certyfikatu SSL (tymczasowo)

Przed skonfigurowaniem Let's Encrypt, utwórz tymczasowy certyfikat:

```bash
# Utwórz katalog dla certyfikatów
mkdir -p docker/nginx/ssl

# Wygeneruj samopodpisany certyfikat (ważny 365 dni)
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout docker/nginx/ssl/key.pem \
  -out docker/nginx/ssl/cert.pem \
  -subj "/C=PL/ST=Mazowieckie/L=Warsaw/O=Development/CN=localhost"
```

### 3.4. Build i uruchomienie kontenerów

```bash
# Build obrazu aplikacji
docker compose build app

# Uruchom wszystkie kontenery w tle
docker compose up -d

# Sprawdź status kontenerów
docker compose ps
```

Wszystkie kontenery powinny być w stanie "Up" i "healthy".

### 3.5. Uruchomienie migracji

```bash
# Poczekaj chwilę aż MySQL będzie gotowy (10-20 sekund)
sleep 15

# Uruchom migracje bazy danych
docker compose exec app php artisan migrate --force

# Opcjonalnie: seedowanie danych testowych
# docker compose exec app php artisan db:seed
```

### 3.6. Optymalizacja Laravel

```bash
# Zoptymalizuj Laravel dla produkcji
docker compose exec app php artisan optimize
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

### 3.7. Sprawdź czy aplikacja działa

```bash
# Sprawdź logi
docker compose logs -f

# Zatrzymaj logi: Ctrl+C

# Sprawdź użycie zasobów
docker stats --no-stream
```

**Test w przeglądarce:**
- Przejdź na `http://YOUR_SERVER_IP` (będzie ostrzeżenie o certyfikacie - to normalne)
- Aplikacja powinna się załadować

---

## Część 4: Konfiguracja domeny + SSL (Let's Encrypt)

### 4.1. Konfiguracja DNS

W panelu swojego dostawcy domeny, dodaj rekordy A:

```
Typ  | Nazwa | Wartość
-----|-------|----------
A    | @     | YOUR_SERVER_IP
A    | www   | YOUR_SERVER_IP
```

Propagacja DNS może zająć od 5 minut do 48 godzin.

**Sprawdź czy DNS działa:**
```bash
# Lokalnie na swoim komputerze
nslookup your-domain.com
```

### 4.2. Instalacja Certbot (Let's Encrypt)

```bash
# Zainstaluj Certbot
sudo apt install certbot -y
```

### 4.3. Wygenerowanie certyfikatu SSL

```bash
# Zatrzymaj nginx tymczasowo (Certbot potrzebuje portu 80)
docker compose stop nginx

# Wygeneruj certyfikat (zamień your-domain.com na swoją domenę)
sudo certbot certonly --standalone \
  -d your-domain.com \
  -d www.your-domain.com \
  --non-interactive \
  --agree-tos \
  --email your-email@example.com

# Certyfikaty zostaną zapisane w /etc/letsencrypt/live/your-domain.com/
```

### 4.4. Skopiuj certyfikaty do projektu

```bash
# Przejdź do katalogu projektu
cd ~/multilanguar-journal-laravel

# Skopiuj certyfikaty
sudo cp /etc/letsencrypt/live/your-domain.com/fullchain.pem docker/nginx/ssl/cert.pem
sudo cp /etc/letsencrypt/live/your-domain.com/privkey.pem docker/nginx/ssl/key.pem

# Ustaw odpowiednie uprawnienia
sudo chown $USER:$USER docker/nginx/ssl/*.pem
```

### 4.5. Zaktualizuj .env z domeną

```bash
nano .env
```

Zmień:
```env
APP_URL=https://your-domain.com
```

### 4.6. Restart nginx

```bash
# Uruchom nginx ponownie
docker compose up -d nginx

# Sprawdź czy działa
docker compose ps nginx
```

### 4.7. Automatyczne odnowienie certyfikatu

Certyfikaty Let's Encrypt ważne są 90 dni. Ustaw automatyczne odnowienie:

```bash
# Edytuj crontab
crontab -e

# Wybierz edytor (nano jest najłatwiejszy)

# Dodaj na końcu pliku (jedna linia):
0 0 * * 0 certbot renew --quiet && cp /etc/letsencrypt/live/your-domain.com/fullchain.pem ~/multilanguar-journal-laravel/docker/nginx/ssl/cert.pem && cp /etc/letsencrypt/live/your-domain.com/privkey.pem ~/multilanguar-journal-laravel/docker/nginx/ssl/key.pem && cd ~/multilanguar-journal-laravel && docker compose restart nginx

# Zapisz: Ctrl+X, Y, Enter
```

To będzie sprawdzać co tydzień (niedziela, północ) czy certyfikat wymaga odnowienia.

**Test:**
Teraz Twoja aplikacja powinna być dostępna na `https://your-domain.com` z prawdziwym certyfikatem SSL! 🎉

---

## Część 5: GitHub Actions Setup (CI/CD)

### 5.1. Wygenerowanie klucza SSH dla GitHub Actions

Na serwerze:

```bash
# Wygeneruj dedykowany klucz dla GitHub Actions
ssh-keygen -t ed25519 -C "github-actions-deploy" -f ~/.ssh/github_actions -N ""

# Dodaj publiczny klucz do authorized_keys
cat ~/.ssh/github_actions.pub >> ~/.ssh/authorized_keys

# Wyświetl prywatny klucz (skopiuj całą zawartość!)
cat ~/.ssh/github_actions
```

**Skopiuj CAŁY prywatny klucz** (włącznie z `-----BEGIN` i `-----END`).

### 5.2. Konfiguracja GitHub Secrets

1. Przejdź na GitHub: `https://github.com/ShoeRiderr/multilanguar-journal-laravel`
2. Kliknij **Settings** → **Secrets and variables** → **Actions**
3. Kliknij **"New repository secret"**
4. Dodaj trzy secrety:

**Secret 1: SSH_PRIVATE_KEY**
- Name: `SSH_PRIVATE_KEY`
- Value: (wklej prywatny klucz z poprzedniego kroku)

**Secret 2: HETZNER_HOST**
- Name: `HETZNER_HOST`
- Value: (IP serwera, np. `123.45.67.89`)

**Secret 3: HETZNER_USER**
- Name: `HETZNER_USER`
- Value: `deploy` (lub `root` jeśli nie utworzyłeś użytkownika deploy)

### 5.3. Test GitHub Actions

```bash
# Lokalnie na swoim komputerze
cd /path/to/your/local/repo

# Zrób jakąś małą zmianę
echo "# Test deployment" >> README.md

# Commit i push do main
git add .
git commit -m "Test: GitHub Actions deployment"
git push origin main
```

Przejdź na GitHub → Actions i sprawdź czy workflow się uruchamia.

### 5.4. Troubleshooting GitHub Actions

Jeśli deployment się nie udaje:

```bash
# Na serwerze, sprawdź logi
docker compose logs -f

# Sprawdź czy są nowe zmiany
git status
git log -1

# Ręcznie uruchom deployment
./deploy.sh
```

---

## Część 6: Monitorowanie i maintenance

### 6.1. Sprawdzanie logów

```bash
# Logi wszystkich kontenerów
docker compose logs -f

# Logi konkretnego kontenera
docker compose logs -f app
docker compose logs -f nginx
docker compose logs -f mysql

# Ostatnie 100 linii logów
docker compose logs --tail=100

# Logi Laravel (w kontenerze)
docker compose exec app tail -f storage/logs/laravel.log
```

### 6.2. Monitorowanie zasobów

```bash
# Użycie zasobów przez kontenery (na żywo)
docker stats

# Użycie dysku
df -h

# Użycie RAM
free -h

# Procesy
htop  # lub: top
```

### 6.3. Backup bazy danych

**Ręczny backup:**
```bash
# Utwórz katalog dla backupów
mkdir -p ~/backups

# Backup bazy danych
docker compose exec mysql mysqldump -u root -p'YOUR_DB_PASSWORD' journal_db > ~/backups/backup_$(date +%Y%m%d_%H%M%S).sql
```

**Automatyczny backup (cron):**
```bash
# Edytuj crontab
crontab -e

# Dodaj codziennie backup o 3:00 AM
0 3 * * * cd ~/multilanguar-journal-laravel && docker compose exec -T mysql mysqldump -u root -p'YOUR_DB_PASSWORD' journal_db > ~/backups/backup_$(date +\%Y\%m\%d).sql && find ~/backups -name "backup_*.sql" -mtime +7 -delete
```

To utworzy codziennie backup i usunie backupy starsze niż 7 dni.

**Restore z backupu:**
```bash
docker compose exec -T mysql mysql -u root -p'YOUR_DB_PASSWORD' journal_db < ~/backups/backup_20240101.sql
```

### 6.4. Aktualizacja aplikacji

**Automatyczna (przez GitHub Actions):**
- Po prostu push do branch `main` → automatyczny deployment

**Ręczna:**
```bash
cd ~/multilanguar-journal-laravel
./deploy.sh
```

### 6.5. Restart kontenerów

```bash
# Restart wszystkich kontenerów
docker compose restart

# Restart konkretnego kontenera
docker compose restart app
docker compose restart nginx

# Restart z przebudowaniem (jeśli zmieniłeś Dockerfile)
docker compose down
docker compose build --no-cache
docker compose up -d
```

### 6.6. Czyszczenie miejsca na dysku

```bash
# Usuń nieużywane obrazy, kontenery i wolumeny
docker system prune -a -f

# Uwaga: to NIE usuwa named volumes (mysql-data, redis-data)

# Sprawdź użycie dysku przez Docker
docker system df
```

---

## Część 7: Optymalizacje i bezpieczeństwo

### 7.1. Bezpieczeństwo SSH

**Wyłącz logowanie root:**
```bash
sudo nano /etc/ssh/sshd_config
```

Znajdź i zmień:
```
PermitRootLogin no
PasswordAuthentication no
```

Restart SSH:
```bash
sudo systemctl restart sshd
```

**UWAGA:** Upewnij się, że możesz się zalogować jako użytkownik `deploy` PRZED restartem SSH!

### 7.2. Firewall (UFW)

```bash
# Zainstaluj i skonfiguruj UFW
sudo apt install ufw -y

# Domyślne reguły
sudo ufw default deny incoming
sudo ufw default allow outgoing

# Zezwól na SSH, HTTP, HTTPS
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Włącz firewall
sudo ufw enable

# Sprawdź status
sudo ufw status
```

### 7.3. Fail2ban (ochrona przed brute-force)

```bash
# Zainstaluj Fail2ban
sudo apt install fail2ban -y

# Skopiuj domyślną konfigurację
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local

# Edytuj konfigurację
sudo nano /etc/fail2ban/jail.local
```

Znajdź sekcję `[sshd]` i upewnij się że jest:
```ini
[sshd]
enabled = true
port = 22
logpath = /var/log/auth.log
maxretry = 3
bantime = 3600
```

Restart Fail2ban:
```bash
sudo systemctl restart fail2ban
sudo systemctl status fail2ban
```

### 7.4. Automatyczne aktualizacje bezpieczeństwa

```bash
# Zainstaluj unattended-upgrades
sudo apt install unattended-upgrades -y

# Włącz automatyczne aktualizacje
sudo dpkg-reconfigure --priority=low unattended-upgrades

# Wybierz "Yes"
```

### 7.5. Monitoring z Uptime Robot (opcjonalnie)

1. Załóż darmowe konto na https://uptimerobot.com
2. Dodaj monitor:
   - Type: HTTPS
   - URL: https://your-domain.com
   - Monitoring Interval: 5 minut
3. Otrzymasz powiadomienie email gdy strona przestanie działać

### 7.6. Hetzner Backup (opcjonalnie, płatne)

W panelu Hetzner:
1. Kliknij na swój serwer
2. **Backups** → Enable
3. Koszt: +20% ceny serwera (~€1.15/mies)
4. Automatyczne backupy codziennie, przechowywane 7 dni

### 7.7. Performance tuning

**Zwiększ limity plików (dla aplikacji z dużym ruchem):**
```bash
sudo nano /etc/security/limits.conf
```

Dodaj:
```
* soft nofile 65535
* hard nofile 65535
```

**Optymalizacja kernela:**
```bash
sudo nano /etc/sysctl.conf
```

Dodaj na końcu:
```
# Network optimization
net.core.somaxconn = 65535
net.ipv4.tcp_max_syn_backlog = 8192
net.ipv4.tcp_fin_timeout = 15
net.ipv4.ip_local_port_range = 1024 65535

# Swap usage (only when needed)
vm.swappiness = 10
```

Zastosuj:
```bash
sudo sysctl -p
```

---

## 🎯 Checklist po deploymencie

- [ ] Aplikacja dostępna na https://your-domain.com
- [ ] Certyfikat SSL jest ważny (zielona kłódka w przeglądarce)
- [ ] Wszystkie kontenery działają: `docker compose ps`
- [ ] Baza danych działa poprawnie
- [ ] GitHub Actions deployment działa
- [ ] Backup bazy danych skonfigurowany
- [ ] Certbot auto-renewal skonfigurowany
- [ ] Firewall włączony
- [ ] SSH zabezpieczone (tylko klucze)
- [ ] Fail2ban włączony

## 🆘 Pomoc i troubleshooting

### Problem: Vite build pada w Dockerze

**Objawy:** `php: not found` podczas `npm run build` w Docker stage

**Rozwiązanie:**
```bash
# Zbuduj assety NA HOŚCIE (przed docker build)
npm install
npm run build

# Sprawdź czy build się udał
ls -la public/build/  # Powinien pokazać pliki manifest.json i inne

# Następnie uruchom Docker
docker compose up -d
```

**Wyjaśnienie:** Plugin Wayfinder wymaga PHP do generowania route types. W Docker multi-stage build Node stage nie ma PHP, więc build zawodzi. Rozwiązanie: budujemy assety na hoście gdzie PHP jest dostępne, a Docker tylko kopiuje gotowe assety.

### Problem: Build folder nie istnieje

**Objawy:** `COPY --from=node /app/public/build - not found` podczas docker build

**Rozwiązanie:** Upewnij się, że `npm run build` zakończył się sukcesem:
```bash
# Sprawdź czy build folder istnieje
ls -la public/build/

# Jeśli nie istnieje, zbuduj ponownie
npm ci
npm run build

# Sprawdź logi pod kątem błędów
echo $?  # Powinno być 0 (sukces)
```

### Problem: Wayfinder errors w Docker build

**Objawy:** Błędy związane z `php artisan wayfinder:generate` podczas `npm run build`

**Rozwiązanie:** Wayfinder wymaga PHP do generowania route types. W Dockerze generujemy go AFTER buildu:
```bash
# Po uruchomieniu kontenerów:
docker compose exec app php artisan wayfinder:generate --with-form

# Lub użyj deploy.sh który robi to automatycznie
./deploy.sh
```

**Alternatywnie:** Użyj uproszczonego Dockerfile (bez buildu assetów w Dockerze):
```bash
# Zbuduj assety lokalnie
npm ci && npm run build

# Uruchom Docker (który używa gotowych assetów)
docker compose up -d
```

### Problem: Kontener się nie uruchamia

```bash
# Sprawdź dokładne logi
docker compose logs [nazwa_kontenera]

# Sprawdź konfigurację
docker compose config
```

### Problem: Brak połączenia z bazą danych

```bash
# Sprawdź czy MySQL działa
docker compose ps mysql

# Sprawdź logi MySQL
docker compose logs mysql

# Test połączenia z kontenera app
docker compose exec app php artisan tinker
# W tinkerze: DB::connection()->getPdo();
```

### Problem: 502 Bad Gateway

```bash
# Sprawdź czy PHP-FPM działa
docker compose ps app

# Sprawdź logi nginx
docker compose logs nginx

# Restart kontenerów
docker compose restart app nginx
```

### Problem: Zabrakło miejsca na dysku

```bash
# Wyczyść Docker
docker system prune -a -f

# Wyczyść logi Laravel
docker compose exec app rm -f storage/logs/*.log

# Sprawdź użycie dysku
df -h
du -sh /var/lib/docker
```

## 📚 Dodatkowe zasoby

- [Laravel Documentation](https://laravel.com/docs)
- [Docker Documentation](https://docs.docker.com)
- [Hetzner Cloud Docs](https://docs.hetzner.com/cloud/)
- [Let's Encrypt](https://letsencrypt.org)
- [Nginx Documentation](https://nginx.org/en/docs/)

---

**Gratulacje!** 🎉 Twoja aplikacja Laravel działa teraz na produkcji z automatycznym deploymentem za mniej niż €6/miesiąc!
