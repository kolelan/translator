FROM php:8.2-cli-alpine3.17

ENV XDEBUG_VERSION 3.2.1

RUN apk add --no-cache grep postgresql-dev bash coreutils git linux-headers libzip-dev zip \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && git clone --branch $XDEBUG_VERSION --depth 1 https://github.com/xdebug/xdebug.git /usr/src/php/ext/xdebug \
    && docker-php-ext-configure xdebug --enable-xdebug-dev \
    && docker-php-ext-install pgsql pdo_pgsql bcmath sockets xdebug zip \
    && apk del git linux-headers

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./conf.d /usr/local/etc/php/conf.d

COPY ./wait-for-it.sh /usr/local/bin/wait-for-it

RUN chmod 555 /usr/local/bin/wait-for-it

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet

RUN addgroup -g 1000 app && adduser -u 1000 -G app -s /bin/sh -D app

WORKDIR /app

USER app

