# Use official PHP image with FPM
FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies, including envsubst
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    libpq-dev \
    netcat-traditional \
    curl \
    zip \
    gettext \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_pgsql zip

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Ensure Laravel directories exist and have correct permissions at build time
RUN mkdir -p bootstrap/cache storage/framework/{views,sessions,cache} storage/logs && \
    chmod -R 775 bootstrap/cache storage

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy Nginx config
COPY docker/nginx/default.conf /etc/nginx/sites-available/default

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Entrypoint
ENTRYPOINT ["sh", "/usr/local/bin/docker-entrypoint.sh"]
