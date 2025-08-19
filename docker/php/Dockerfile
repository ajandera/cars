FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    zlib1g-dev libzip-dev unzip git libicu-dev libpq-dev libsqlite3-dev \
    && docker-php-ext-install pdo_pgsql pdo_sqlite intl zip

RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony
    
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /srv/app