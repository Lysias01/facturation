FROM richarvey/nginx-php-fpm:latest

# Set environment variables
ENV WEBROOT=/var/www/html/public
ENV PHP_UPLOAD_MAX_FILESIZE=50M
ENV PHP_MAX_FILE_UPLOADS=100

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

# Create .env from .env.example if it doesn't exist
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Generate APP_KEY if not set
RUN php artisan key:generate --force || echo "APP_KEY will be set via env vars"

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Cache Laravel config
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache || true

# Configure nginx
RUN echo "location / {" >> /etc/nginx/conf.d/default.conf && \
    echo "    try_files \$uri \$uri/ /index.php?\$query_string;" >> /etc/nginx/conf.d/default.conf && \
    echo "}" >> /etc/nginx/conf.d/default.conf
