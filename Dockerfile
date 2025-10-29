# -------------------------------
# Stage 0: Build
# -------------------------------
FROM php:8.3-fpm AS build

# Set working directory
WORKDIR /var/www

# Install system dependencies (including Node.js & npm for Vite)
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    libpq-dev \
    netcat \
    curl \
    zip \
    gettext \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_pgsql zip

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copy Laravel app files
COPY . .

# Set permissions for Laravel storage & cache directories
RUN mkdir -p bootstrap/cache storage && \
    chmod -R 775 bootstrap/cache storage

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install frontend dependencies & build assets
RUN npm install
RUN npm run build

# Set proper permissions for public/build
RUN chown -R www-data:www-data public/build

# -------------------------------
# Stage 1: Production
# -------------------------------
FROM php:8.3-fpm

WORKDIR /var/www

# Copy PHP app & vendor from build stage
COPY --from=build /var/www /var/www

# Install system dependencies for Nginx
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    curl \
    gettext \
    && rm -rf /var/lib/apt/lists/*

# Copy Nginx config
COPY docker/nginx/default.conf /etc/nginx/sites-available/default

# Copy entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose PHP-FPM port
EXPOSE 9000

# Set entrypoint
ENTRYPOINT ["sh", "/usr/local/bin/docker-entrypoint.sh"]
