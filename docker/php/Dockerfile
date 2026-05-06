FROM php:8.3-apache

# Install PHP extensions yang dibutuhkan
RUN docker-php-ext-install pdo_mysql

# Enable Apache mod_rewrite (untuk URL routing)
RUN a2enmod rewrite

# Set document root ke folder public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Update Apache config untuk menggunakan document root baru
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf

# Allow .htaccess overrides
RUN sed -ri -e 's/AllowOverride None/AllowOverride All/g' \
    /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Copy source code (hanya digunakan saat production build)
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
