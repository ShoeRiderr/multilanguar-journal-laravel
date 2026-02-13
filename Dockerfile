# Multi-stage Dockerfile zoptymalizowany dla Hetzner CX23 (8GB RAM)
# Laravel 12 + Inertia.js + Vue 3 + TypeScript

# Stage 1: Base - PHP 8.2 z rozszerzeniami
FROM php:8.2-fpm-alpine AS base

# Instalacja zależności systemowych i rozszerzeń PHP
RUN apk add --no-cache \
    mysql-client \
    postgresql-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    zip \
    unzip \
    git \
    curl

# Konfiguracja i instalacja rozszerzeń PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        pdo_pgsql \
        zip \
        gd \
        intl \
        opcache \
        bcmath \
        mbstring \
        exif \
        pcntl

# Instalacja Redis extension
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

# Instalacja Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Ustawienie katalogu roboczego
WORKDIR /var/www/html

# Stage 2: Node - budowanie assetów
FROM node:20-alpine AS node

WORKDIR /app

# Kopiowanie plików package
COPY package*.json ./

# Instalacja zależności Node
RUN npm ci --only=production=false

# Kopiowanie kodu źródłowego
COPY . .

# Budowanie assetów z Vite
RUN npm run build

# Stage 3: Production - finalna aplikacja
FROM base AS production

# Kopiowanie konfiguracji PHP
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Kopiowanie kodu aplikacji
COPY --chown=www-data:www-data . /var/www/html

# Instalacja zależności Composer (bez dev, z optymalizacją)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Kopiowanie zbudowanych assetów z stage Node
COPY --from=node --chown=www-data:www-data /app/public/build /var/www/html/public/build

# Ustawienie uprawnień dla storage i bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Przełączenie na użytkownika www-data
USER www-data

# Expose port dla PHP-FPM
EXPOSE 9000

# Komenda startowa
CMD ["php-fpm"]
