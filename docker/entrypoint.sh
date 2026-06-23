#!/bin/sh

# Exit immediately if a command exits with a non-zero status
set -e

echo "Running environment setup..."

# Cache configuration, routes, and views for production optimization
echo "Caching Laravel bootstrap configs..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

echo "Starting services..."
# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
echo "Nginx started."
exec nginx -g 'daemon off;'
