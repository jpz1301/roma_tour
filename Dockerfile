FROM php:8.4-apache

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql \
    && a2enmod rewrite

COPY . /var/www/html/