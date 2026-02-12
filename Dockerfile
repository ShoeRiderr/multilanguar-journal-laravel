# Multi-stage Dockerfile dla Laravel 12 + Inertia.js + Vue 3 + TypeScript

# ==========================================
# Base Stage - PHP 8.2-FPM z rozszerzeniami
# ==========================================
FROM php:8.2-fpm AS base

# Instalacja zależności systemowych
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install zip \
    && docker-php-ext-install intl \
    && docker-php-ext-install opcache \
    && docker-php-ext-install bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalacja Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Ustawienie katalogu roboczego
WORKDIR /var/www/html

# Kopiowanie plików composer
COPY composer.json composer.lock ./

# Instalacja zależności PHP (bez dev dla produkcji)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# ==========================================
# Node Stage - Budowanie assetów frontend
# ==========================================
FROM node:20-alpine AS node

WORKDIR /app

# Kopiowanie plików package
COPY package.json package-lock.json ./

# Instalacja zależności npm
RUN npm ci

# Kopiowanie kodu źródłowego frontend
COPY . .

# Budowanie assetów produkcyjnych
RUN npm run build

# ==========================================
# Production Stage - Finalna konfiguracja
# ==========================================
FROM php:8.2-fpm AS production

# Instalacja zależności systemowych (identycznie jak base)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install zip \
    && docker-php-ext-install intl \
    && docker-php-ext-install opcache \
    && docker-php-ext-install bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalacja Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Ustawienie katalogu roboczego
WORKDIR /var/www/html

# Kopiowanie zależności vendor z base stage
COPY --from=base /var/www/html/vendor ./vendor

# Kopiowanie kodu aplikacji
COPY . .

# Kopiowanie zbudowanych assetów z node stage
COPY --from=node /app/public/build ./public/build

# Dokończenie instalacji Composer z autoloaderem
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Kopiowanie konfiguracji PHP
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Ustawienie uprawnień dla użytkownika www-data
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Przełączenie na użytkownika www-data
USER www-data

# Expose port dla PHP-FPM
EXPOSE 9000

# Uruchomienie PHP-FPM
CMD ["php-fpm"]
