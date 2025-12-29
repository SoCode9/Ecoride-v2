# Installation des dépendances PHP avec Composer
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --ignore-platform-req=ext-mongodb

# Image finale : environnement PHP / Apache de l’application
FROM php:8.2-apache

# Installation des dépendances système et extensions PHP nécessaires au projet
RUN apt-get update && apt-get install -y \
    libicu-dev libpng-dev libzip-dev unzip git \
    libssl-dev pkg-config \
    && docker-php-ext-install pdo_mysql mysqli intl gd zip \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Déploiement du code de l’application
WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor /var/www/html/vendor

# Droits adaptés pour Apache
RUN chown -R www-data:www-data /var/www/html

# Port exposé par le serveur web
EXPOSE 80

