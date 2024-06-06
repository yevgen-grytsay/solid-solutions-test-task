FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libmcrypt-dev \
    && docker-php-ext-install -j$(nproc) pdo_mysql \
    && docker-php-ext-enable pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www
#RUN composer install --no-interaction

# The main purpose of a CMD is to provide defaults for an executing container. These defaults can include an executable,
# or they can omit the executable, in which case you must specify an ENTRYPOINT instruction as well.
CMD ["php", "-S 0.0.0.0:80", "-t /var/www/public"]
