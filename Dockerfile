# Multi-stage Dockerfile for Laravel + Vue.js on Render.com
# Stage 1: Build Node.js assets
FROM node:20-alpine AS node_builder

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm ci --production=false

# Copy source files needed for build
COPY vite.config.js ./
COPY tailwind.config.js ./
COPY postcss.config.js ./
COPY resources ./resources

# Build assets
RUN npm run build

# Stage 2: PHP Runtime
FROM php:8.1-fpm-alpine

# Install system dependencies
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

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_pgsql \
    pgsql \
    mbstring \
    bcmath \
    zip \
    gd \
    exif \
    pcntl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies (production only)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application files
COPY . .

# Copy built assets from node_builder stage
COPY --from=node_builder /app/public/build ./public/build

# Generate optimized autoloader
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/start.sh /usr/local/bin/start.sh

# Make start script executable
RUN chmod +x /usr/local/bin/start.sh

# Create directories for nginx and supervisor
RUN mkdir -p /var/log/supervisor \
    && mkdir -p /run/nginx

# Expose port (Render uses PORT env variable)
EXPOSE 8080

# Start application via supervisor
CMD ["/usr/local/bin/start.sh"]
