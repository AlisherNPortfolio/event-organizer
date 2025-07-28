FROM php:8.4-fpm-alpine

WORKDIR /var/www/html

# COPY . .

# COPY php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf

# Setup GD extension
RUN apk add --no-cache \
      freetype \
      libjpeg-turbo \
      libpng \
      freetype-dev \
      libjpeg-turbo-dev \
      libpng-dev \
    && docker-php-ext-configure gd \
      --with-freetype=/usr/include/ \
      --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-enable gd \
    && apk del --no-cache \
      freetype-dev \
      libjpeg-turbo-dev \
      libpng-dev \
    && rm -rf /tmp/*

# Install intl extension
# RUN apk add --no-cache \
#     icu-dev \
#     && docker-php-ext-install -j$(nproc) intl \
#     && docker-php-ext-enable intl \
#     && rm -rf /tmp/*

# Install mbstring extension
# RUN apk add --no-cache \
#     oniguruma-dev \
#     && docker-php-ext-install mbstring \
#     && docker-php-ext-enable mbstring \
#     && rm -rf /tmp/*

# INstall opcache, xdebug, redis, mongodb
# RUN docker-php-source extract \
#     && pecl install opcache xdebug redis mongodb apcu \
#     && echo "xdebug.remote_enable=on\n" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
#     && echo "xdebug.remote_autostart=on\n" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
#     && echo "xdebug.remote_port=9000\n" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
#     && echo "xdebug.remote_handler=dbgp\n" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
#     && echo "xdebug.remote_connect_back=1\n" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
#     && docker-php-ext-enable opcache xdebug redis mongodb apcu \
#     && docker-php-source delete \
#     && rm -rf /tmp/*

RUN docker-php-ext-install pdo pdo_mysql

# RUN docker-php-ext-install pdo pdo_mysql \
#     && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
#     && chown -R www-data:www-data /var/www/html
