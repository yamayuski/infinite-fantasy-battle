ARG PHP_VERSION=8.3-cli-alpine

ARG RR_VERSION=2024

ARG EXTENSION_INSTALLER_VERSION=2

FROM spiralscout/roadrunner:${RR_VERSION} AS rr
FROM mlocati/php-extension-installer:${EXTENSION_INSTALLER_VERSION} AS ext

FROM php:${PHP_VERSION}

COPY --from=ext /usr/bin/install-php-extensions /usr/local/bin/install-php-extensions
RUN install-php-extensions opcache pdo_sqlite protobuf gettext intl redis zip sockets @composer-2

COPY --from=rr /usr/bin/rr /usr/local/bin/rr

WORKDIR /src

RUN chown www-data:www-data /src

USER www-data

COPY composer.* .

RUN composer install --no-autoloader

COPY . .

RUN composer dump-autoload

EXPOSE 8080

CMD [ "rr", "serve", "-c", "/src/.rr.yaml" ]
