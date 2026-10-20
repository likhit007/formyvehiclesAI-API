#!/bin/sh
set -e

# Discover packages now that environment variables are loaded
php artisan package:discover --ansi

# Clear and optimize configuration caches
php artisan config:clear
php artisan route:clear

# Start the Laravel application
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"