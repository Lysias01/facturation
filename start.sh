#!/bin/bash
set -e

# Laravel utilise Render env vars direct (pas besoin .env file)

# Key Laravel (force override)
php artisan key:generate --force --no-interaction --quiet

# DB setup
php artisan migrate --force --no-interaction
php artisan db:seed --class=UserSeeder

# Caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Perms Apache
chmod -R 777 storage bootstrap/cache

# Apache
exec apache2-foreground
