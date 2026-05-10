FROM shinsenter/frankenphp:latest

# Copiar todo el proyecto a la carpeta de trabajo
COPY . /var/www/html/

# Ajustar permisos
RUN chown -R www-data:www-data /var/www/html/

WORKDIR /var/www/html
