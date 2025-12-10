#!/bin/bash
set -e

echo "=========================================="
echo "Starting Laravel Application"
echo "=========================================="

# Configure Nginx port from environment variable (default 8080)
PORT=${PORT:-8080}
echo "Configuring Nginx to listen on port $PORT..."
sed -i "s/listen 8080;/listen $PORT;/" /etc/nginx/nginx.conf

# Clear Laravel caches to force reading .env
echo "Clearing Laravel caches to apply environment variables..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Wait for PostgreSQL database to be ready
echo "Waiting for PostgreSQL database at $DB_HOST:$DB_PORT..."
until pg_isready -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME"; do
  echo "Database not ready, waiting..."
  sleep 2
done

echo "Database connection established!"

# Run migrations
# echo "Running database migrations..."
# php artisan migrate --force --no-interaction

# Install Passport (OAuth2 server)
# echo "Installing Laravel Passport..."
# php artisan passport:install --force --no-interaction || true

# Seed initial data (roles and users)
# echo "Seeding initial data..."
# php artisan db:seed --class=InitialDataSeeder --force --no-interaction || true

# Cache routes and views for performance (do NOT cache config in production)
echo "Caching routes and views..."
php artisan route:cache
php artisan view:cache

# Verify HealthController exists
if [ -f "/var/www/html/app/Http/Controllers/HealthController.php" ]; then
    echo "✓ HealthController.php found"
else
    echo "✗ HealthController.php NOT FOUND!"
    exit 1
fi

# Test the /health endpoint
echo "Testing /health endpoint..."
php artisan tinker --execute="echo \Illuminate\Support\Facades\Route::dispatch(\Illuminate\Http\Request::create('/health', 'GET'))->getContent();" 2>&1 || echo "Health endpoint test failed"

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
