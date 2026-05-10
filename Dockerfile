FROM php:8.4-cli

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql

WORKDIR /var/www/html
COPY . /var/www/html/

CMD php -S 0.0.0.0:${PORT:-8080} -t /var/www/html