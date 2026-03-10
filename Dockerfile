FROM php:8.2-cli

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    npm \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    zip \
    exif \
    pcntl \
    bcmath \
    gd

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir dossier de travail
WORKDIR /app

# Copier projet
COPY . .

# Installer dépendances Laravel
RUN composer install --no-dev --optimize-autoloader

# Installer et compiler assets
RUN npm install && npm run build

# Exposer port Render
EXPOSE 10000

# Lancer Laravel
CMD php artisan serve --host=0.0.0.0 --port=10000
