FROM php:8.4-cli

ENV DEBIAN_FRONTEND=noninteractive \
    COMPOSER_MEMORY_LIMIT=-1 \
    APP_ENV=production \
    APP_DEBUG=false

WORKDIR /app

# 1. Install System Dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip curl ca-certificates libonig-dev libxml2-dev libzip-dev \
        libpng-dev libjpeg-dev libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# 2. Install PHP Extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
        bcmath bz2 curl exif fileinfo gd intl mbstring opcache pcntl \
        pdo_mysql redis simplexml tokenizer xml xmlreader xmlwriter zip openssl \
    && rm -rf /tmp/pear

# 3. Install Composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# 4. Copy Composer Files
COPY composer.json composer.lock ./

# 5. FIX: Force Update Lock & Install
# Jika 'laravel/octane' belum ada di lock, command ini akan menambahkannya.
# --ignore-platform-req=php mencegah error versi PHP saat update lock.
RUN composer update --lock --no-scripts --no-interaction --ignore-platform-req=php \
    && composer install \
        --optimize-autoloader \
        --no-dev \
        --prefer-dist \
        --no-interaction \
        --no-scripts \
        --ignore-platform-req=php \
    && rm -rf ~/.composer/cache

# 6. Copy Source Code
# PENTING: Folder 'vendor' TIDAK akan tercopy jika .dockerignore sudah benar.
COPY --chown=www-data:www-data . /app

# 7. Copy PHP Config
COPY ./Deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini

# 8. Final Permissions & Cleanup
RUN rm -rf /app/bootstrap/cache/* /app/storage/framework/cache/* /app/storage/framework/sessions/* /app/storage/framework/views/* \
    && mkdir -p /var/www/.octane \
    && chown -R www-data:www-data /app/vendor /app/storage /app/bootstrap/cache /var/www/.octane

# 9. Laravel Optimization (Build Time)
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan event:cache \
    && (php artisan storage:link || true)

# 10. Switch to Non-Root User
USER www-data

EXPOSE 8000

# 11. Start Command
CMD ["sh", "-c", "php artisan config:clear && php artisan route:clear && php artisan optimize && php artisan octane:start --server=frankenphp --host=0.0.0.0 --port=8000 --workers=14 --max-requests=500"]
