FROM php:7.2.1-apache-stretch

RUN apt-get update && apt-get install -y \
   zip \
   && docker-php-source extract \
   && docker-php-ext-install -j$(nproc) pdo_sqlite \
   && docker-php-source delete

COPY . /var/www/

RUN /var/www/util/prepareImage.sh
