FROM php:8.2-fpm

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Application environment
WORKDIR /var/www

## Install system packages for PHP extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
    @composer \
    gd \
    pdo_mysql \
    zip \
    bcmath \
    mbstring \
    exif \
    gmagick \
    dom \
    iconv \
    intl \
    soap \
    opcache \
    json \
    curl \
    xdebug
#    mongodb \
#    memcached \
#    amqp \
#    redis \
#    pdo_pgsql \