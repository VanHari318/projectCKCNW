# Stage 1: Build Frontend Assets (Vite)
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY package*.json vite.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/
RUN npm ci && npm run build

# Stage 2: Production PHP and Nginx Environment
FROM php:8.2-fpm-alpine

# Install system dependencies & build tools for PHP extensions
RUN apk add --no-nginx --no-cache \
    nginx \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    postgresql-dev \
    bash

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql zip gd bcmath opcache

# Copy custom Nginx configuration
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Setup working directory
WORKDIR /var/www/html

# Copy project files (excluding those in .dockerignore)
COPY . .

# Copy compiled assets from node-builder stage
COPY --from=node-builder /app/public/build ./public/build

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install production dependencies
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Set permissions for Laravel storage and cache directories
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Make entrypoint script executable
RUN chmod +x /var/www/html/docker/entrypoint.sh

# Expose HTTP port
EXPOSE 80

# Execute entrypoint script
ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]
