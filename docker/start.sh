#!/bin/bash
set -e

echo "=========================================="
echo "Starting Laravel Application on Render.com"
echo "=========================================="

# Wait for database to be ready
echo "Waiting for database connection..."
until php artisan migrate:status 2>/dev/null || [ $? -eq 1 ]; do
  echo "Database not ready, waiting..."
  sleep 2
done

echo "Database connection established!"

# Run migrations
echo "Running database migrations..."
php artisan migrate --force --no-interaction

# Install Passport (OAuth2 server)
echo "Installing Laravel Passport..."
php artisan passport:install --force || true

# Clear and cache configurations
echo "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ensure proper permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

echo "=========================================="
echo "Application ready! Starting services..."
echo "=========================================="

# Start supervisord (manages nginx + php-fpm)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
