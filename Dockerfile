# ============================
# BUILD STAGE - Laravel App
# ============================
FROM php:8.2-fpm-alpine AS app

# Installer packages nécessaires
RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    postgresql-client \
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

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers Composer et installer les dépendances
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-scripts --optimize-autoloader
RUN composer dump-autoload --optimize

# Copier l'application Laravel
COPY . .

# Configurer Nginx et Supervisor
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/start.sh /usr/local/bin/start.sh

# Donner les droits d’exécution au start.sh
RUN chmod +x /usr/local/bin/start.sh

# Permissions correctes pour Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Exposer le port (Koyeb redirige vers ce port)
EXPOSE 8080

# Lancer le start.sh au démarrage
CMD ["/usr/local/bin/start.sh"]

# ============================
# FINAL STAGE - Serveur Nginx + PHP-FPM
# ============================