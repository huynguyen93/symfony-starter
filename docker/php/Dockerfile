ARG COMPOSER_VERSION=2.2.7

FROM composer:${COMPOSER_VERSION} AS composer
FROM php:8.0-fpm

RUN apt-get update && apt-get install -y \
    vim \
    ffmpeg \
    openssl \
    git \
    unzip

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone
RUN printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini
RUN "date"

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer --version

RUN docker-php-ext-install pdo pdo_mysql

# install extension "zip"
RUN apt-get update && apt-get install -y libzip-dev
RUN docker-php-ext-install zip

# install extension "intl"
RUN apt-get update && apt-get install -y libicu-dev
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl

# install extension "xdebug"
RUN install-php-extensions xdebug

# install extension "gd"
#RUN apt-get install -y libwebp-dev libjpeg62-turbo-dev libpng-dev libxpm-dev libfreetype6-dev zlib1g-dev
#RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp
#RUN docker-php-ext-install gd
RUN install-php-extensions gd

ARG _UID=1000
ARG _GID=1000

RUN usermod -u ${_UID} www-data && groupmod -g ${_GID} www-data

COPY root/ /

ENTRYPOINT ["docker-entrypoint-dev"]

WORKDIR /var/www/symfony

CMD ["php-fpm"]
