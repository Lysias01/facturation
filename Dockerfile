FROM php:8.3-apache

# Install system deps
RUN apt-get update &amp;&amp; apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libnode-dev \
    &amp;&amp; docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip \
    &amp;&amp; apt-get clean &amp;&amp; rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy code
COPY . .

# Composer install
RUN composer install --no-dev --optimize-autoloader

# NPM
RUN npm install &amp;&amp; npm run build

# Apache config
RUN a2enmod rewrite
RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Laravel
RUN chown -R www-data:www-data /var/www/html
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

EXPOSE 80

CMD ["apache2-foreground"]
