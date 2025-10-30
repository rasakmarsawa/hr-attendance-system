#!/bin/sh

set -e

# -------------------------------
# Wait for database
# -------------------------------
echo "Waiting for database at $DB_HOST:$DB_PORT..."
while ! nc -z "$DB_HOST" "$DB_PORT"; do
  sleep 2
done
echo "Database is ready."

# -------------------------------
# Run Laravel migrations & seeders
# -------------------------------
echo "Running Laravel migrations & seeders..."
php artisan migrate:fresh --seed --force

# -------------------------------
# Replace ${PORT} in Nginx config
# -------------------------------
echo "Configuring Nginx to listen on port $PORT..."
if command -v envsubst >/dev/null 2>&1; then
    envsubst '$PORT' < /etc/nginx/sites-available/default > /etc/nginx/sites-available/default.tmp
    mv /etc/nginx/sites-available/default.tmp /etc/nginx/sites-available/default
else
    echo "envsubst not found! Ensure gettext is installed."
    exit 1
fi

# -------------------------------
# Start PHP-FPM
# -------------------------------
echo "Starting PHP-FPM..."
php-fpm &

# Wait a moment for PHP-FPM to fully start
sleep 2

# -------------------------------
# Start Nginx in foreground
# -------------------------------
echo "Starting Nginx..."
nginx -g "daemon off;"
