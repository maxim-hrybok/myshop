# Use official PHP image with Apache
FROM php:8.2-apache

# Enable Apache mod_rewrite (Required for FastRoute & .htaccess)
RUN a2enmod rewrite

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd pdo pdo_mysql

# Change ownership of the web root to the apache user
RUN chown -R www-data:www-data /var/www/html

# Set working directory
WORKDIR /var/www/html