
FROM richarvey/nginx-php-fpm:latest

COPY . .

ENV SKIP_COMPOSER=1
ENV WEBROOT=/var/www/html/public
ENV PHP_ERRORS_STDERR=1
ENV RUN_SCRIPTS=1

RUN echo "=== CHECKING LARAVEL DEPLOYMENT ===" \
    && ls -la /var/www/html \
    && ls -la /var/www/html/public \
    && ls -la /var/www/html/bootstrap \
    && ls -la /var/www/html/vendor \
    && test -f /var/www/html/public/index.php \
    && test -f /var/www/html/vendor/autoload.php

EXPOSE 80
