FROM php:8.4-fpm-alpine

WORKDIR /var/www/html

COPY . .

# COPY php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf

RUN docker-php-ext-install pdo pdo_mysql \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html
