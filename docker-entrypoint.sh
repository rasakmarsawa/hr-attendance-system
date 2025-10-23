#!/bin/sh

echo "Waiting for MySQL..."
while ! mysqladmin ping -h "$DB_HOST" -u "$DB_USERNAME" -p"$DB_PASSWORD" --ssl=0 --silent; do    
    sleep 2
done

php artisan migrate:fresh --seed
php artisan serve --host=0.0.0.0 --port=8000
