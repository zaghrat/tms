FROM php:8.0-apache

RUN docker-php-ext-install pdo_mysql


COPY ./app /var/www/html
COPY ./sys/etc/apache2/sites-available /etc/apache2/sites-available


WORKDIR /var/www/html


EXPOSE 80
