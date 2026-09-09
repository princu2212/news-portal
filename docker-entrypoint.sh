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

# Set full read/write permissions
chmod -R 777 /var/www/storage /var/www/bootstrap/cache /var/www/database

# Clear any cached configuration
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "Running migrations..."
php artisan migrate --force

echo "Seeding categories, districts, and articles..."
php artisan db:seed --force

echo "Fetching live RSS feeds (IBC24, DPRCG, Vistaar)..."
php artisan news:fetch-rss || true

# Cache routes and views for optimal speed
php artisan route:cache || true
php artisan view:cache || true

# Bind to the port assigned by Render ($PORT) or fallback to 8080
PORT="${PORT:-8080}"

# Start background worker to auto-fetch new RSS feeds every 5 minutes
(while true; do sleep 300; php artisan news:fetch-rss > /dev/null 2>&1; done &)

echo "CG News Express starting on port $PORT..."
exec php artisan serve --host=0.0.0.0 --port="$PORT"
