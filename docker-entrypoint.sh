#!/bin/sh
set -e

# Ensure SQLite database exists
if [ ! -f /var/www/database/database.sqlite ]; then
    touch /var/www/database/database.sqlite
fi

# Ensure correct permissions
chmod -R 777 /var/www/storage /var/www/bootstrap/cache /var/www/database

# Run database migrations and seed default data
php artisan migrate --force || true
php artisan db:seed --force || true

# Fetch initial RSS feed articles
php artisan news:fetch-rss || true

# Clear and optimize configuration
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Determine port (Render and Fly set $PORT)
PORT="${PORT:-8080}"

echo "Starting Laravel News Portal on port $PORT..."
exec php artisan serve --host=0.0.0.0 --port="$PORT"
