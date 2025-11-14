#!/bin/sh
set -e

# -------------------------------
# Wait for database
# -------------------------------
echo "Waiting for database at $DB_HOST:$DB_PORT..."
while ! nc -z "$DB_HOST" "$DB_PORT"; do
  echo "Database not ready, retrying..."
  sleep 2
done
echo "Database is ready."

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# -------------------------------
# Run Laravel migrations & seeders
# -------------------------------
echo "Running Laravel migrations & seeders..."
php artisan migrate:fresh --seed --force

# -------------------------------
# Start PHP-FPM in foreground
# -------------------------------
echo "Starting PHP-FPM..."
php-fpm
