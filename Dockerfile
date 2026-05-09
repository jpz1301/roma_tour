FROM php:8.4-apache

# Instalar extensión pgsql y otras dependencias comunes
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql pdo pdo_pgsql \
    && a2enmod rewrite

# Document root de Apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html

# Copiar todo el proyecto a la carpeta de Apache
COPY . /var/www/html/

# Ajustar permisos
RUN chown -R www-data:www-data /var/www/html/