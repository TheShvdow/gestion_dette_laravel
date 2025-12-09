# ============================
# 1) BUILD STAGE - Composer
# ============================
FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-scripts --no-autoloader

# ============================
# 2) BUILD STAGE - Laravel App
# ============================
FROM php:8.2-fpm-alpine AS app

# Packages nécessaires
RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    curl \
    git \
    unzip \
    bash

# PHP Extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
       pdo_pgsql pgsql mbstring bcmath zip gd exif pcntl

# Working directory
WORKDIR /var/www/html

# Copy composer vendor stage
COPY --from=vendor /app/vendor ./vendor

# Copy Laravel
COPY . .

# Composer autoload optimize
RUN composer dump-autoload --optimize

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Config Nginx / Supervisor
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/start.sh /usr/local/bin/start.sh

RUN chmod +x /usr/local/bin/start.sh

EXPOSE 8080

CMD ["/usr/local/bin/start.sh"]
