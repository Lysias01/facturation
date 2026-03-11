FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (removed pdo_pgsql - MySQL only)
RUN docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# Copy application
COPY . /var/www/html

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set permissions
RUN chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Enable .htaccess for Apache
COPY public/.htaccess /var/www/html/public/.htaccess

EXPOSE 8080

# Fix permissions
RUN chmod -R 755 /var/www/html/public
RUN chmod -R 755 /var/www/html/storage
RUN chmod -R 755 /var/www/html/bootstrap/cache


CMD ["apache2-foreground"]
