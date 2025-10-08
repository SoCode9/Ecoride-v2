# Dockerfile
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --ignore-platform-req=ext-mongodb

FROM php:8.2-apache

# Paquets + extensions PHP
RUN apt-get update && apt-get install -y \
    libicu-dev libpng-dev libzip-dev unzip git \
    libssl-dev pkg-config \
    && docker-php-ext-install pdo_mysql mysqli intl gd zip \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Copie du code
WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor /var/www/html/vendor
## COPY docker/config/default.conf /etc/apache2/sites-available/000-default.conf

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

