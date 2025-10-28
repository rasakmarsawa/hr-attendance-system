# Use official PHP image with FPM
FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    libpq-dev \
    netcat-traditional \
    && docker-php-ext-install pdo_pgsql pdo_mysql zip

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copy app files
COPY . .

# Create storage and bootstrap/cache directories
RUN mkdir -p bootstrap/cache storage && \
    chmod -R 775 bootstrap/cache storage

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port 9000 (FPM)
EXPOSE 9000

# Set entrypoint
ENTRYPOINT ["sh", "/usr/local/bin/docker-entrypoint.sh"]
