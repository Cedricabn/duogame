FROM php:8.2-fpm-alpine

# Dépendances système
RUN apk add --no-cache \
    nginx supervisor \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    libzip-dev zip unzip git curl \
    oniguruma-dev \
    mysql-client

# Extensions PHP
RUN docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && cp .env.example .env \
    && php artisan key:generate \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Config Nginx + Supervisor
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/ca.pem /var/www/docker/ca.pem

EXPOSE 8080

CMD ["sh", "-c", "php artisan migrate --force && supervisord -c /etc/supervisord.conf"]