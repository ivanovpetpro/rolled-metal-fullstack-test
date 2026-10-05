FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    bash \
    build-base \
    git \
    unzip \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    zlib-dev \
    curl \
    gnupg \
    autoconf \
    make \
    file \
    mariadb-connector-c-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure zip \
    && docker-php-ext-install pdo pdo_mysql zip intl opcache \
    && pecl install apcu && docker-php-ext-enable apcu \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony \
    && git config --global user.email "ivanov.vladimir.v@yandex.ru" \
    && git config --global user.name "Vladimir Ivanov"

WORKDIR /var/www/html

EXPOSE 9000
CMD ["php-fpm"]