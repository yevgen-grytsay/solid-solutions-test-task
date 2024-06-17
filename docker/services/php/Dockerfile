FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    libzip-dev \
    libmcrypt-dev \
    unzip \
    && docker-php-ext-install -j$(nproc) pdo_mysql zip \
    && docker-php-ext-enable pdo_mysql

RUN pecl install xdebug-3.3.2 \
    && docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www
#RUN composer install --no-interaction

# The main purpose of a CMD is to provide defaults for an executing container. These defaults can include an executable,
# or they can omit the executable, in which case you must specify an ENTRYPOINT instruction as well.
CMD ["php", "-S 0.0.0.0:80", "-t /var/www/public"]
