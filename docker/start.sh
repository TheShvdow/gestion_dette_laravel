#!/bin/bash
set -e

echo "=========================================="
echo "Starting Laravel Application"
echo "=========================================="

# Debug: Print environment variables (without sensitive data)
echo "Environment check:"
echo "DB_HOST: ${DB_HOST:-NOT_SET}"
echo "DB_PORT: ${DB_PORT:-NOT_SET}"
echo "DB_DATABASE: ${DB_DATABASE:-NOT_SET}"
echo "DB_USERNAME: ${DB_USERNAME:-NOT_SET}"
echo "DATABASE_URL present: ${DATABASE_URL:+YES}"

PORT=${PORT:-8080}
sed -i "s/listen 8080;/listen $PORT;/" /etc/nginx/nginx.conf

# Clear Laravel caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Parse DATABASE_URL if DB_HOST is not set
if [ -z "$DB_HOST" ] && [ -n "$DATABASE_URL" ]; then
  echo "DB_HOST not set, parsing from DATABASE_URL..."
  # Extract host, port, database, username, password from DATABASE_URL
  # Format: postgres://username:password@host:port/database
  DB_HOST=$(echo $DATABASE_URL | sed -n 's|.*@\([^:]*\):.*|\1|p')
  DB_PORT=$(echo $DATABASE_URL | sed -n 's|.*:\([0-9]*\)/.*|\1|p')
  DB_DATABASE=$(echo $DATABASE_URL | sed -n 's|.*/\([^?]*\).*|\1|p')
  DB_USERNAME=$(echo $DATABASE_URL | sed -n 's|.*://\([^:]*\):.*|\1|p')
  DB_PASSWORD=$(echo $DATABASE_URL | sed -n 's|.*://[^:]*:\([^@]*\)@.*|\1|p')

  echo "Extracted from DATABASE_URL:"
  echo "DB_HOST: $DB_HOST"
  echo "DB_PORT: $DB_PORT"
  echo "DB_DATABASE: $DB_DATABASE"
  echo "DB_USERNAME: $DB_USERNAME"
fi

# Validate required variables
if [ -z "$DB_HOST" ]; then
  echo "ERROR: DB_HOST is not set and could not be parsed from DATABASE_URL"
  exit 1
fi

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