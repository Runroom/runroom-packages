FROM alpine:3.17

ARG PHP_VERSION=81
ARG UID=1000
ARG USER=app

RUN apk add --no-cache \
    php${PHP_VERSION} \
    php${PHP_VERSION}-gd \
    php${PHP_VERSION}-intl \
    php${PHP_VERSION}-opcache \
    php${PHP_VERSION}-zip \
    php${PHP_VERSION}-phar \
    php${PHP_VERSION}-iconv \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-openssl \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-dom \
    php${PHP_VERSION}-tokenizer \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-simplexml \
    php${PHP_VERSION}-xmlreader \
    php${PHP_VERSION}-xmlwriter \
    php${PHP_VERSION}-session \
    php${PHP_VERSION}-pdo_sqlite \
    php${PHP_VERSION}-fileinfo \
    php${PHP_VERSION}-pecl-pcov --repository=https://dl-cdn.alpinelinux.org/alpine/edge/testing

RUN adduser -u $UID -D $USER

ENV PATH="/usr/app/vendor/bin:/usr/app/bin:${PATH}"

COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

USER ${USER}

WORKDIR /usr/app
