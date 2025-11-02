# syntax=docker/dockerfile:1

################################################################################
# Composer Deps Stage
################################################################################

FROM composer:lts AS deps

WORKDIR /app

RUN --mount=type=bind,source=composer.json,target=composer.json \
    --mount=type=bind,source=composer.lock,target=composer.lock \
    --mount=type=cache,target=/tmp/cache \
    composer install --no-dev --no-interaction --ignore-platform-reqs

################################################################################
# PHP Build Stage
################################################################################

FROM php:8.3-apache AS final

LABEL org.opencontainers.image.source=https://github.com/adam-rms/adam-rms
LABEL org.opencontainers.image.documentation=https://adam-rms.com/self-hosting
LABEL org.opencontainers.image.url=https://adam-rms.com
LABEL org.opencontainers.image.vendor="Bithell Studios Ltd."
LABEL org.opencontainers.image.description="AdamRMS MVP image with PHP Apache2 runtime."
LABEL org.opencontainers.image.licenses=AGPL-3.0

ENV APACHE_DOCUMENT_ROOT=/var/www/html/src \
    SEED_ON_START=true

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql mysqli intl zip

RUN a2enmod rewrite

RUN echo "\npost_max_size=64M\n" >> "$PHP_INI_DIR/php.ini" \
    && echo "memory_limit=256M\n" >> "$PHP_INI_DIR/php.ini" \
    && echo "max_execution_time=600\n" >> "$PHP_INI_DIR/php.ini" \
    && echo "sys_temp_dir=/tmp\n" >> "$PHP_INI_DIR/php.ini" \
    && echo "upload_max_filesize=64M\n" >> "$PHP_INI_DIR/php.ini"

RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf \
    && sed -ri -e "s!/var/www/!${APACHE_DOCUMENT_ROOT}/!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY --from=deps /app/vendor/ /var/www/html/vendor
COPY ./src /var/www/html/src
COPY ./db /var/www/html/db
COPY ./phinx.php /var/www/html
COPY ./migrate.sh /var/www/html
COPY ./.env.example /var/www/html/.env.example

RUN chmod +x /var/www/html/migrate.sh

USER www-data

SHELL ["sh"]
ENTRYPOINT ["/var/www/html/migrate.sh"]
