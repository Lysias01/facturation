FROM php:8.2-cli

# Installer extensions nécessaires
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    zip \
    curl \
    npm \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN npm install && npm run build

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=10000
