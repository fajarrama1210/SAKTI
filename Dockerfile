FROM php:8.4-cli

WORKDIR /app

# 1. Install helper ekstensi & dependensi sistem
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip curl ca-certificates \
    && install-php-extensions \
        gd zip pcntl opcache pdo_mysql bcmath intl exif redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Composer versi terbaru (2.8+ wajib untuk PHP 8.4 & Laravel 11+)
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# 3. Cache layer: copy hanya file composer dulu
COPY composer.json composer.lock ./
RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
        --optimize-autoloader \
        --no-dev \
        --prefer-dist \
        --no-scripts \
    && rm -rf ~/.composer/cache

# 4. Copy source code & konfigurasi PHP
COPY --chown=www-data:www-data . /app
COPY ./deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini

# 5. Siapkan folder auto-download FrankenPHP (hindari error permission saat runtime)
RUN mkdir -p /var/www/.octane && chown -R www-data:www-data /var/www/.octane

# 6. Optimasi Laravel & atur permission production
RUN php artisan storage:link \
    && php artisan optimize \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/vendor

# 7. Drop privilege ke non-root
USER www-data

EXPOSE 8000

# 8. Start Octane (FrankenPHP akan otomatis dipakai dari PATH jika tersedia)
CMD ["php", "artisan", "octane:start", "--workers=14", "--server=frankenphp", "--host=0.0.0.0", "--port=8000"]
