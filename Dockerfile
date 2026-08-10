FROM php:8.2-cli

WORKDIR /var/www/html

# System deps
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    curl \
    zip \
    && docker-php-ext-install pdo pdo_mysql zip

# Use composer image to copy composer binary
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy app
COPY . /var/www/html

# Install PHP dependencies (assumes composer.lock is present)
RUN composer install --no-dev --optimize-autoloader --no-interaction || true

# Ensure storage and cache are writable
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

EXPOSE 8080

# Note: for production you should run queue workers, scheduler and use a proper webserver (nginx).
# This image runs the Laravel built-in server which is sufficient for small self-hosted deployments.
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
