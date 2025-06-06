FROM php:8.1-fpm

RUN docker-php-ext-install pdo pdo_mysql


WORKDIR /var/www/html

COPY . /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader
RUN mkdir -p /var/www/html/logs && chmod -R 777 /var/www/html/logs
