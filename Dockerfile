# Simplified Dockerfile for Hetzner CX23 (8GB RAM)
# Laravel 12 + Inertia.js + Vue 3 + TypeScript
#
# IMPORTANT: Assets MUST be built on the host BEFORE running docker build!
# This is required because Wayfinder needs access to PHP (via docker compose exec)
# to generate TypeScript types during the asset build process.
#
# Build flow:
# 1. docker compose up -d app mysql  # Start backend (PHP available)
# 2. npm run build                   # Build assets on host
# 3. docker compose build app        # Build this image (copies public/build)
#
# Or simply run: ./deploy.sh

# Stage 1: Composer dependencies
FROM composer:latest AS composer-deps

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --optimize-autoloader

# Stage 2: Production - PHP-FPM
FROM php:8.2-fpm-alpine

# Instaluj zależności systemowe
RUN apk add --no-cache \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    postgresql-dev \
    zip \
    unzip \
    mysql-client \
    bash

# Instaluj rozszerzenia PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache

# Instalacja Redis extension
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

# Konfiguracja PHP
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Workdir
WORKDIR /var/www/html

# Kopiuj vendor z stage 1
COPY --from=composer-deps /app/vendor /var/www/html/vendor

# Kopiuj całą aplikację (łącznie z public/build zbudowanym NA HOŚCIE)
COPY --chown=www-data:www-data . /var/www/html

# Autoloader
RUN composer dump-autoload --optimize --no-dev

# Uprawnienia dla Laravel
RUN mkdir -p storage/framework/{cache,sessions,views} \
    storage/logs \
    bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

USER www-data

EXPOSE 9000

CMD ["php-fpm"]
