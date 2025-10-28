#!/bin/sh

# Wait for MySQL port to respond
while ! nc -z "$DB_HOST" "$DB_PORT"; do
  sleep 2
done

echo "MySQL is ready. Running migrations..."

php artisan migrate:fresh --seed

echo "Starting Laravel..."

php-fpm &

nginx -g "daemon off;"
