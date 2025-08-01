FROM php:8.0-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev zip unzip git curl libpng-dev libjpeg-dev libwebp-dev libfreetype6-dev \
    libonig-dev libxml2-dev libzip-dev pkg-config \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
        gd pdo pdo_pgsql bcmath zip exif opcache intl

# Install Redis
RUN pecl install redis \
    && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

#  Create non-root user (ID 1000 to match most Linux users like WSL)
RUN groupadd -g 1000 laravel && \
    useradd -u 1000 -g laravel -m -s /bin/bash laravel

# Set ownership for the working directory
RUN chown -R laravel:laravel /var/www/html

# Switch to non-root user
USER laravel

