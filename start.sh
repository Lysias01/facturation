#!/bin/bash
set -e

# Créer .env si absent (Render injecte vars)
if [ ! -f .env ]; then
  cp .env.example .env
fi

# Key Laravel
php artisan key:generate --force --no-interaction

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
