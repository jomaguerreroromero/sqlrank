
FROM php:8.2-apache

# Instala PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Copiar tu código al contenedor
COPY . /var/www/html/

# Exponer el puerto 80
EXPOSE 80
