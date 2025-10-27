# Use official PHP image
FROM php:8.2-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install dependencies
COPY composer.json /var/www/html/
RUN apt-get update && apt-get install -y git unzip \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-interaction

# Copy project files
COPY . /var/www/html/

# Expose port 80
EXPOSE 80
CMD ["apache2-foreground"]
