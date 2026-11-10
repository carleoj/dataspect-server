FROM php:8.3-fpm

# Install system dependencies and Nginx
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    gettext-base \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    unzip \
    && docker-php-ext-install \
        pdo_pgsql \
        pgsql \
        mbstring \
        bcmath \
        pcntl \
        zip \
        xml \
        curl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Laravel working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install production dependencies
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# Set Laravel permissions
RUN mkdir -p storage/framework/cache \
             storage/framework/sessions \
             storage/framework/views \
             storage/logs \
             bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Configure Nginx
RUN rm -f /etc/nginx/sites-enabled/default \
    && cat > /etc/nginx/conf.d/default.conf <<'EOF'
server {
    listen 0.0.0.0:${PORT};
    server_name _;
    root /var/www/html/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    location ~ /\. {
        deny all;
    }
}
EOF

# Startup script to clear stale caches and launch services
RUN cat > /usr/local/bin/start-container <<'EOF'
#!/bin/sh
set -e

: "${PORT:=10000}"
export PORT

envsubst '${PORT}' \
    < /etc/nginx/conf.d/default.conf \
    > /tmp/default.conf

mv /tmp/default.conf /etc/nginx/conf.d/default.conf

# Clear and rebuild Laravel caches at runtime to pick up latest .env & cors.php
php artisan config:clear
php artisan route:clear
php artisan cache:clear

php-fpm -D

exec nginx -g 'daemon off;'
EOF

RUN chmod +x /usr/local/bin/start-container

EXPOSE 10000

CMD ["/usr/local/bin/start-container"]