# -------------------------------
# Stage 0: Build
# -------------------------------
FROM php:8.3-fpm AS build

WORKDIR /var/www

# Install system dependencies for build
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    libpq-dev \
    curl \
    zip \
    gnupg \
    ca-certificates \
    netcat-traditional \
    && rm -rf /var/lib/apt/lists/*

# Install Node.js 20.x
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_pgsql zip

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copy app
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Build frontend assets
RUN npm install
RUN npm run build

# -------------------------------
# Stage 1: Production
# -------------------------------
FROM php:8.3-fpm

WORKDIR /var/www

# Copy everything from build stage
COPY --from=build /var/www /var/www

# Install runtime dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    curl \
    netcat-traditional \
    gettext \
    && rm -rf /var/lib/apt/lists/*

# Set proper permissions for Laravel
RUN mkdir -p storage/framework/{views,sessions,cache} bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Copy Nginx config
COPY docker/nginx/default.conf /etc/nginx/sites-available/default

# Copy entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose PHP-FPM port
EXPOSE 9000

ENTRYPOINT ["sh", "/usr/local/bin/docker-entrypoint.sh"]
