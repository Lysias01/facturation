FROM php:8.2-apache

# Enable Apache mod_rewrite and headers
RUN a2enmod rewrite headers

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

# Install PHP extensions (with PostgreSQL support)
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring zip exif pcntl bcmath gd

# Copy composer files first for caching
COPY composer.json composer.lock /var/www/html/

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependencies (skip scripts to avoid artisan errors)
WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Install Node.js dependencies and build
RUN npm install && npm run build

# Copy the rest of the application
COPY . /var/www/html

# Set permissions
RUN chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public

# Copy startup script
COPY start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]
