#!/bin/bash
set -e

# Enable Apache mod_rewrite
a2enmod rewrite

# Create Apache config to allow .htaccess
cat > /etc/apache2/sites-available/000-default.conf << 'EOF'
<VirtualHost *:80>
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
        
        # Handle Laravel routing
        FallbackResource /index.php
    </Directory>

    <Directory /var/www/html>
        Options -Indexes +FollowSymLinks
        AllowOverride None
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

# Enable the site
a2ensite 000-default.conf

# Ensure storage directories have correct permissions
chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public

# Generate Laravel key if not set
php /var/www/html/artisan key:generate --no-interaction --force

# Run Laravel optimizations
php /var/www/html/artisan config:cache --force
php /var/www/html/artisan route:cache --force
php /var/www/html/artisan view:cache --force

# Start Apache in foreground
exec apache2-foreground

