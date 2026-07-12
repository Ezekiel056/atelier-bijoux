FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y zlib1g-dev g++ git libicu-dev libzip-dev libpng-dev libwebp-dev libjpeg-dev zip \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install gd \
    && docker-php-ext-enable gd \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

RUN a2enmod rewrite && a2enmod ssl && a2enmod socache_shmcb

WORKDIR /var/www/html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

RUN git config --global user.email "francois.crequer@gmail.com"
RUN git config --global user.name "François Crequer"