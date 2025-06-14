FROM composer:2.8.6 AS vendor_installer

WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./
RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader \
    --no-dev

FROM node:lts-alpine AS asset_builder
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install
COPY vite.config.js ./
COPY /resources ./resources
RUN npm run build

FROM breakhack/php-cli:8.2

WORKDIR /var/www/html
COPY --from=vendor_installer /app/vendor/ /var/www/html/vendor/
COPY --from=asset_builder /app/public/build /var/www/html/public/build
COPY php.ini-production /usr/local/etc/php/php.ini
COPY --chown=1000:1000 . .
RUN php artisan storage:link

EXPOSE 8000
CMD ["/bin/sh", "-c", "php artisan migrate --force && php artisan serve --host 0.0.0.0 --port 8000"]
