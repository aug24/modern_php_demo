FROM php:5-apache
RUN docker-php-ext-install sockets pdo pdo_mysql mysqli
COPY . /var/www/html
EXPOSE 80
