FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    zip \
    libzip-dev \
    pkg-config \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . /var/www/html

RUN mkdir -p /var/www/html/Core/JWT \
    && touch /var/www/html/Core/JWT/blacklist.json \
    && chmod -R 666 /var/www/html/Core/JWT/blacklist.json

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader

RUN mkdir -p /var/www/html/logs && chmod -R 777 /var/www/html/logs
