FROM php:8.2-apache

RUN apt-get update && \
    apt-get install -y \
        libssl-dev \
        libcurl4-openssl-dev

RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql

RUN pecl install mongodb
RUN docker-php-ext-enable mongodb

RUN echo "extension=pdo_mysql.so" >> /usr/local/etc/php/php.ini

RUN a2enmod rewrite
RUN service apache2 restart
