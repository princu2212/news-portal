#!/bin/sh
set -e

cd /var/www

# Copy .env.example if .env does not exist
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Ensure all critical storage & database directories exist
mkdir -p /var/www/storage/framework/sessions \
         /var/www/storage/framework/views \
         /var/www/storage/framework/cache \
         /var/www/storage/logs \
         /var/www/bootstrap/cache \
         /var/www/database

# Ensure SQLite database file exists
if [ ! -f /var/www/database/database.sqlite ]; then
    touch /var/www/database/database.sqlite
fi

# Set open permissions so web process has full read/write access
chmod -R 777 /var/www/storage /var/www/bootstrap/cache /var/www/database

# Clear any cached configuration from build stage
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Run database migrations and seed data
php artisan migrate --force || true
php artisan db:seed --force || true

# Fetch initial RSS articles in background so web server starts immediately
(php artisan news:fetch-rss > /dev/null 2>&1 &)

# Cache routes and views for optimal performance
php artisan route:cache || true
php artisan view:cache || true

# Bind to the port assigned by Render ($PORT) or fallback to 8080 / 10000
PORT="${PORT:-8080}"

echo "CG News Express starting on port $PORT..."
exec php artisan serve --host=0.0.0.0 --port="$PORT"
