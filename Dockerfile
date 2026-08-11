FROM node:22 AS node-builder

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --silent

COPY . .
RUN npm run build --silent


FROM php:8.3-cli AS php-builder

WORKDIR /app

RUN apt-get update && apt-get install -y \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    gd \
    mbstring \
    zip \
    xml \
    pdo \
    pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-progress

FROM php:8.3-cli

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    gd \
    mbstring \
    zip \
    xml \
    pdo \
    pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=php-builder /app/vendor ./vendor

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