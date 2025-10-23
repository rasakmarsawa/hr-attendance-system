# Use official PHP image with FPM
FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    netcat-traditional \
    && docker-php-ext-install pdo_mysql zip

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copy app files
COPY . .

RUN mkdir -p bootstrap/cache storage && \
    chmod -R 775 bootstrap/cache storage

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port
EXPOSE 9000
ENTRYPOINT ["sh", "/usr/local/bin/docker-entrypoint.sh"]

