FROM wordpress:latest

# Install PHP extensions
RUN docker-php-ext-install mysqli

# Copy theme files
COPY aureon/theme /var/www/html/wp-content/themes/aureon
COPY aureon/frontend /var/www/html/wp-content/themes/aureon/frontend
COPY aureon/plugin /var/www/html/wp-content/plugins/aureon
COPY aureon/ferm-page.php /var/www/html/wp-content/themes/aureon/ferm-page.php

# Set permissions
RUN chown -R www-data:www-data /var/www/html/wp-content/themes/aureon
RUN chown -R www-data:www-data /var/www/html/wp-content/plugins/aureon

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
