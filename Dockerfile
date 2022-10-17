FROM php:8.1.0-apache

#php setup, install extensions, setup configs
RUN apt-get update && apt-get install --no-install-recommends -y \
    libzip-dev \
    libxml2-dev \
    mariadb-client \
    zip \
    libicu-dev \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*


RUN pecl install zip pcov
RUN docker-php-ext-enable zip \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install soap \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-source delete

COPY ./app /var/www/html
COPY ./sys/etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/000-default.conf

# install composer
WORKDIR /tmp
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');"  \
    && mv composer.phar /usr/local/bin/composer

# install 3rd party bundles
WORKDIR /var/www/html
RUN composer install


CMD ["apache2-foreground"]