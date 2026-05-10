FROM shinsenter/frankenphp:latest

<<<<<<< HEAD
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql \
    && a2enmod rewrite

COPY . /var/www/html/
=======
# Copiar todo el proyecto a la carpeta de trabajo
COPY . /var/www/html/

# Ajustar permisos
RUN chown -R www-data:www-data /var/www/html/

WORKDIR /var/www/html
>>>>>>> 68aba27cd692e91e487202e588bf2e25ea9bcc88
