#!/bin/sh

set -e

# --- Wait for database to be ready ---
echo "Waiting for database at $DB_HOST:$DB_PORT..."
while ! nc -z "$DB_HOST" "$DB_PORT"; do
  sleep 2
done
echo "Database is ready."

# --- Run migrations and seeders ---
echo "Running Laravel migrations & seeders..."
php artisan migrate:fresh --seed --force

# --- Replace ${PORT} in nginx config with actual Render port ---
echo "Configuring Nginx to listen on port $PORT..."
envsubst '$PORT' < /etc/nginx/sites-available/default > /etc/nginx/sites-available/default.tmp
mv /etc/nginx/sites-available/default.tmp /etc/nginx/sites-available/default

# --- Start PHP-FPM in background ---
echo "Starting PHP-FPM..."
php-fpm &

# --- Start Nginx in foreground ---
echo "Starting Nginx..."
nginx -g "daemon off;"
