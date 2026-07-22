# Use official PHP image with Apache
FROM php:8.2-apache

# Enable Apache mod_rewrite
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

# Copy the application source code into the container
COPY . /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies (ignores dev requirements for production)
RUN composer install --no-dev --optimize-autoloader

# Create necessary directories if they don't exist
RUN mkdir -p /var/www/html/cache /var/www/html/templates_c /var/www/html/public/uploads/products

# Set correct ownership and permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/cache \
    && chmod -R 775 /var/www/html/templates_c \
    && chmod -R 775 /var/www/html/public/uploads

WORKDIR /var/www/html