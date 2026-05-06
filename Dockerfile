# Using the PHP 8.2 as Apache base image
FROM php:8.2-apache

# Enable the Apache module rewrite
RUN a2enmod rewrite

# Install PHP extensions for MySQL database connection
RUN docker-php-ext-install mysqli pdo_mysql pdo

# Set document root ke folder public (entry point: public/index.php)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf

# Allow .htaccess overrides (untuk URL routing)
RUN sed -ri -e 's/AllowOverride None/AllowOverride All/g' \
    /etc/apache2/apache2.conf