FROM php:8.4-fpm-alpine3.20

WORKDIR /var/www/html

RUN <<EOF
apk add --no-cache --update \
    $PHPIZE_DEPS \
    linux-headers \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    mysql-client

docker-php-ext-configure gd --with-freetype --with-jpeg
docker-php-ext-configure zip

docker-php-ext-install -j$(nproc) \
  gd \
  zip \
  pdo_mysql \
  exif \
  intl \
  opcache \
  pcntl

pecl install -o -f redis
docker-php-ext-enable redis

apk del $PHPIZE_DEPS linux-headers
EOF

COPY --from=composer /usr/bin/composer /usr/bin/composer

EXPOSE 9000

CMD ["php-fpm"]
