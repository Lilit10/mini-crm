FROM php:8.4-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    bash \
    git \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    mysql-client \
    $PHPIZE_DEPS \
  && docker-php-ext-install -j"$(nproc)" \
    intl \
    pdo \
    pdo_mysql \
    zip \
  && php -m | grep -q pdo_mysql \
  && rm -rf /tmp/* /var/cache/apk/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

CMD ["php-fpm"]
