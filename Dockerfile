FROM shinsenter/frankenphp:latest

# Instalar extensión pgsql y otras dependencias comunes
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql pdo pdo_pgsql

# Copiar todo el proyecto a la carpeta de trabajo
COPY . /var/www/html/

# Ajustar permisos
RUN chown -R www-data:www-data /var/www/html/

WORKDIR /var/www/html

CMD ["frankenphp", "php-server", "--listen", "0.0.0.0:80", "--root", "/var/www/html"]