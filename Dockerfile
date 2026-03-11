FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy only composer files first (for better caching)
COPY composer.json composer.lock ./

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP dependencies (skip post-install scripts to avoid APP_KEY error)
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs --no-scripts
RUN composer dump-autoload --optimize --ignore-platform-reqs

# Copy the rest of the application
COPY . /var/www/html

# Set permissions
RUN chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port
EXPOSE 8000

# Start Apache and PHP
CMD ["apache2-foreground"]
