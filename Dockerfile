FROM php:8.1.0-apache

#php setup, install extensions, setup configs
RUN apt-get update && apt-get install --no-install-recommends -y \
    libzip-dev \
    libxml2-dev \
    mariadb-client \
    zip \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN pecl install zip pcov
RUN docker-php-ext-enable zip \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install soap \
    && docker-php-source delete


# install composer
RUN cd /tmp
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

# install 3rd party bundles
RUN cd /var/www/html
RUN composer install


CMD ["apache2-foreground"]