FROM richarvey/nginx-php-fpm:latest

# Set environment variables
ENV WEBROOT=/var/www/html/public

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

# Create .env from .env.example if it doesn't exist
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Generate APP_KEY if not set
RUN php artisan key:generate --force || true

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Cache Laravel config
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache || true
