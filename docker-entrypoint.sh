#!/bin/sh
set -e

# Wait/Run optimizations
php artisan config:clear
php artisan route:clear

# Start PHP built-in server bound to Render's assigned PORT
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"