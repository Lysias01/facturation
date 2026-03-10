FROM php:8.2-cli

# Installer extensions et dépendances nécessaires
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    zip \
    curl \
    npm \
    libpq-dev \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer depuis l'image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /app

# Copier le projet
COPY . .

# Installer les dépendances PHP et optimiser l’autoloader
RUN composer install --no-dev --optimize-autoloader

# Installer et compiler les assets frontend
RUN npm install && npm run build

# Exposer le port de l’application
EXPOSE 10000

# Lancer le serveur Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=10000"]
