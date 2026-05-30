FROM php:8.4-cli

ENV DEBIAN_FRONTEND=noninteractive \
    COMPOSER_MEMORY_LIMIT=-1 \
    APP_ENV=production \
    APP_DEBUG=false

WORKDIR /app

# 1. System dependencies & apt update (FIX #3: Update sebelum install)
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip curl ca-certificates libonig-dev libxml2-dev libzip-dev \
        libpng-dev libjpeg-dev libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# 2. Install PHP extensions via helper
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
        bcmath bz2 curl exif fileinfo gd intl mbstring opcache pcntl \
        pdo_mysql redis simplexml tokenizer xml xmlreader xmlwriter zip openssl \
    && rm -rf /tmp/pear

# 3. Composer binary (Versi terbaru untuk PHP 8.4)
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# 4. Composer install (FIX Exit Code 4: Bypass platform check)
COPY composer.json composer.lock ./
RUN composer install \
        --optimize-autoloader \
        --no-dev \
        --prefer-dist \
        --no-interaction \
        --no-scripts \
        --ignore-platform-req=php \
    && rm -rf ~/.composer/cache

# 5. Copy app source code & php.ini
COPY --chown=www-data:www-data . /app
COPY ./Deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini

# 6. Fix Permissions & Clean Local Cache (FIX #1 & #4)
# Hapus cache lokal yang mungkin ikut tercopy dari git, lalu chown vendor SEBELUM pindah user
RUN rm -rf /app/bootstrap/cache/* /app/storage/framework/cache/* /app/storage/framework/sessions/* /app/storage/framework/views/* \
    && mkdir -p /var/www/.octane \
    && chown -R www-data:www-data /app/vendor /app/storage /app/bootstrap/cache /var/www/.octane

# 7. Laravel Optimization (Build Time)
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan event:cache \
    && (php artisan storage:link || true)

# 8. Switch to non-root user (FIX #1: Setelah chown vendor selesai)
USER www-data

EXPOSE 8000

# 9. Start Command (FIX #5: JSON Array + Clear Cache Runtime)
CMD ["sh", "-c", "php artisan config:clear && php artisan route:clear && php artisan optimize && php artisan octane:start --server=frankenphp --host=0.0.0.0 --port=8000 --workers=14 --max-requests=500"]
