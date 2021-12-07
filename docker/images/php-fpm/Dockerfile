FROM php:8.1-fpm AS php-fpm

LABEL maintainer="Adam Kirk <adam_kirk@live.co.uk>"

RUN apt-get update
RUN apt-get install -y git

# Require for symfony validators
RUN apt-get install -y libicu-dev && docker-php-ext-configure intl && docker-php-ext-install intl

# Required for composer (prevents warnings)
RUN apt-get install -y libzip-dev zip unzip && docker-php-ext-install zip

# Cleanup some files...probably needs to be more thorough
RUN apt-get clean all -y

COPY php-fpm.conf /usr/local/etc/
COPY php.prod.ini /usr/local/etc/php/php.ini
COPY php-fpm.d/www.conf.template /usr/local/etc/php-fpm.d/www.conf.template

RUN mkdir /opt/app-root
WORKDIR /opt/app-root

COPY --from=composer:2.1 /usr/bin/composer /usr/bin/composer
RUN mkdir -p /var/www/.composer/cache

COPY ./entrypoint.sh /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

FROM php-fpm AS cron

# Install cron
RUN apt-get update
RUN apt-get -y install cron

RUN rm -rf /etc/cron.daily/*
RUN rm -rf /etc/cron.weekly/*
RUN rm -rf /etc/cron.monthly/*

# Make sure to log to Docker
RUN mkfifo /var/log/cron.log
RUN ln -sf /dev/stdout /var/log/cron.log

# Setup start script
COPY ./cron/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

FROM php-fpm AS dev

COPY php.dev.ini /usr/local/etc/php/php.ini

COPY dev-entrypoint.sh /dev-entrypoint.sh
RUN chmod +x /dev-entrypoint.sh

ENTRYPOINT ["/dev-entrypoint.sh"]

FROM cron AS cron-dev

COPY php.dev.ini /usr/local/etc/php/php.ini

COPY dev-entrypoint.sh /dev-entrypoint.sh
RUN chmod +x /dev-entrypoint.sh

ENTRYPOINT ["/dev-entrypoint.sh"]
