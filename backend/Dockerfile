ARG PHP_VERSION=8.3.11-cli-alpine3.20

ARG RR_VERSION=2024.2.0

FROM php:${PHP_VERSION}

RUN useradd -aG rr

COPY --from=mlocati/php-extension-installer --chown=rr:rr /usr/bin/install-php-extensions /usr/loca/bin/install-php-extensions
COPY --from=spiralscout/roadrunner:${RR_VERSION} --chown=rr:rr /usr/bin/rr /usr/local/bin/rr

RUN install-php-extension opcache pdo_mysql protobuf gettext intl redis zip
