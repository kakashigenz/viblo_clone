FROM php:8.2-fpm-alpine

WORKDIR /home/site
RUN apk add --no-cache zip libzip-dev
RUN docker-php-ext-install mysqli && docker-php-ext-install pdo_mysql && docker-php-ext-install zip
