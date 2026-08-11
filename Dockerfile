FROM composer:2 AS composer-installer

WORKDIR /app

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-progress


FROM node:18 AS node-builder

WORKDIR /app

COPY package*.json ./

RUN npm install

COPY resources ./resources
COPY public ./public
COPY vite.config.js ./

RUN npm run build


FROM php:8.3-cli

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libsqlite3-dev \
    pkg-config \
    sqlite3 \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo \
    pdo_sqlite \
    mbstring \
    zip \
    xml \
    gd \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer-installer /app/vendor ./vendor

COPY . .

COPY --from=node-builder /app/public/build ./public/build

RUN mkdir -p \
    database \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && if [ ! -f database/database.sqlite ]; then touch database/database.sqlite; fi

RUN php artisan package:discover --ansi

EXPOSE 8080

CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]