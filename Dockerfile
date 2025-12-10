# ============================
# BUILD STAGE - Laravel App
# ============================
FROM php:8.2-fpm-alpine AS app

# ============================
# Installer les paquets nécessaires
# ============================
RUN apk add --no-cache \
    bash \
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
    bash \
    shadow

# ============================
# Installer les extensions PHP
# ============================
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
       pdo_pgsql pgsql mbstring bcmath zip gd exif pcntl

# ============================
# Installer Composer
# ============================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ============================
# Définir le répertoire de travail
# ============================
WORKDIR /var/www/html

# ============================
# Copier composer.json et composer.lock pour installer les dépendances
# ============================
COPY composer.json composer.lock ./

# Installer les dépendances sans exécuter les scripts (artisan n’existe pas encore)
RUN composer install --no-dev --prefer-dist --no-scripts --optimize-autoloader

# ============================
# Copier l’application Laravel
# ============================
COPY . .

# ============================
# Maintenant artisan existe, on peut exécuter les scripts post-install
# ============================
RUN composer dump-autoload --optimize \
    && php artisan package:discover --ansi

# ============================
# Permissions correctes
# ============================
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# ============================
# Copier la configuration Nginx et Supervisor
# ============================
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# ============================
# Exposer le port
# ============================
EXPOSE 8080

# ============================
# Commande par défaut
# ============================
CMD ["/usr/local/bin/start.sh"]
