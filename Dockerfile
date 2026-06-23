# syntax=docker/dockerfile:1

FROM php:8.3-cli-bookworm AS composer_deps

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libcurl4-openssl-dev \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libxml2-dev \
        libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        curl \
        dom \
        gd \
        intl \
        mbstring \
        opcache \
        pdo_mysql \
        simplexml \
        xml \
        xmlwriter \
        zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && composer install --no-dev --optimize-autoloader --no-interaction

FROM node:22-bookworm-slim AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY --from=composer_deps /app/vendor ./vendor
COPY resources ./resources
COPY public ./public
COPY postcss.config.js tailwind.config.js vite.config.js ./

RUN npm run build

FROM php:8.3-apache-bookworm AS app

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    PORT=8080

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        curl \
        default-mysql-client \
        libcurl4-openssl-dev \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libxml2-dev \
        libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        curl \
        dom \
        gd \
        intl \
        mbstring \
        opcache \
        pdo_mysql \
        simplexml \
        xml \
        xmlwriter \
        zip \
    && a2enmod rewrite headers expires remoteip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY --from=composer_deps --chown=www-data:www-data /app /var/www/html
COPY --from=frontend --chown=www-data:www-data /app/public /var/www/html/public

COPY docker/apache-vhost.conf /etc/apache2/sites-available/campusflow.conf.template
COPY docker/entrypoint.sh /usr/local/bin/campusflow-entrypoint
COPY docker/import-demo-data.sh /usr/local/bin/campusflow-import-demo-data

RUN chmod +x /usr/local/bin/campusflow-entrypoint /usr/local/bin/campusflow-import-demo-data \
    && mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache

EXPOSE 8080

ENTRYPOINT ["campusflow-entrypoint"]
