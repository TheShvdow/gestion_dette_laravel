#!/bin/bash
set -e

echo "=========================================="
echo "Starting Laravel Application"
echo "=========================================="

PORT=${PORT:-8080}
sed -i "s/listen 8080;/listen $PORT;/" /etc/nginx/nginx.conf

# Clear Laravel caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Wait for database
echo "Waiting for PostgreSQL database at $DB_HOST:$DB_PORT..."
until PGPASSWORD=$DB_PASSWORD psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" -d "$DB_DATABASE" -c '\q' 2>/dev/null; do
  echo "Database not ready, waiting..."
  sleep 2
done

echo "Database connection established!"

# Cache routes/views
php artisan route:cache
php artisan view:cache

# Permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Start supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

echo "Laravel Application started on port $PORT"