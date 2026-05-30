FROM php:8.4-cli

ENV DEBIAN_FRONTEND=noninteractive \
    COMPOSER_MEMORY_LIMIT=-1 \
    APP_ENV=production \
    APP_DEBUG=false

WORKDIR /app

# [FIX 3] Layer terpisah: apt-get update + install system deps
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip curl ca-certificates libonig-dev libxml2-dev libzip-dev \
        libpng-dev libjpeg-dev libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions helper
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
        bcmath bz2 curl exif fileinfo gd intl mbstring opcache pcntl \
        pdo_mysql redis simplexml tokenizer xml xmlreader xmlwriter zip openssl \
    && rm -rf /tmp/pear

# Install Composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# Composer install (dijalankan sebagai ROOT)
COPY composer.json composer.lock ./
RUN composer install \
        --optimize-autoloader \
        --no-dev \
        --prefer-dist \
        --no-interaction \
        --no-scripts \
    && rm -rf ~/.composer/cache

# Copy aplikasi & config PHP
COPY --chown=www-data:www-data . /app
COPY ./deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini

# [FIX 1 & 4] Hapus cache lokal yang ikut tercopy, siapkan folder, & set permission vendor sebelum pindah user
RUN rm -rf /app/bootstrap/cache/* /app/storage/framework/cache/* /app/storage/framework/sessions/* /app/storage/framework/views/* \
    && mkdir -p /var/www/.octane \
    && chown -R www-data:www-data /app/vendor /app/storage /app/bootstrap/cache /var/www/.octane

# Build-time optimization (aman karena dijalankan sekali saat image dibuat)
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan event:cache \
    && php artisan storage:link || true

# [FIX 1] Pindah ke non-root user setelah permission vendor beres
USER www-data

EXPOSE 8000

# [FIX 4 & 5] CMD format JSON Array + clear cache sebelum optimize/start
CMD ["sh", "-c", "php artisan config:clear && php artisan route:clear && php artisan optimize && php artisan octane:start --server=frankenphp --host=0.0.0.0 --port=8000 --workers=14 --max-requests=500"]
