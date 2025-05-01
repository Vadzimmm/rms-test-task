FROM php:8.4-cli-alpine

ARG UID=1000

ENV PHP_INI_SCAN_DIR=/usr/local/etc/php/conf.d:/usr/local/etc/php/app-conf.d

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN set set -xeu pipefail \
    && apk update \
    # Install PHP deps
    && apk update && apk add --no-cache -t .deps $PHPIZE_DEPS \
        icu-dev \
        libpng-dev \
        libzip-dev \
        linux-headers \
        shadow \
        zlib-dev \
    && docker-php-ext-install \
        gd \
        intl \
        pdo_mysql \
        zip \
    && pecl install xdebug && docker-php-ext-enable xdebug \
    && usermod -u "${UID}" www-data \
    && groupmod -g "${UID}" www-data \
    && mkdir /app && chown www-data:www-data /app

WORKDIR /app

EXPOSE 80

CMD ["php", "-S", "0.0.0.0:80", "-t", "public"]
