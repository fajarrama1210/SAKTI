FROM php:8.3-cli

# 1. Set working directory
WORKDIR /app

# 2. Gunakan mlocati/install-php-extensions - menangani semua dependensi otomatis
#    Tidak perlu apt-get install manual → lebih stabil & tidak error nama paket
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
        gd \
        zip \
        pcntl \
        opcache \
        pdo_mysql \
        bcmath \
        intl \
        exif \
        xmlwriter \
        xmlreader \
        redis \
    && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/*

# 3. Copy konfigurasi PHP kustom
COPY ./Deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini

# 4. Setup Composer & install dependencies
#    Copy composer files dulu (layer cache: tidak re-install jika kode saja yang berubah)
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./

RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
        --optimize-autoloader \
        --no-dev \
        --prefer-dist \
        --no-scripts \
    && rm -rf ~/.composer/cache

# 5. Salin seluruh kode aplikasi (setelah vendor ter-install = layer cache lebih efisien)
COPY --chown=www-data:www-data . /app

# 6. Hapus file statis yang berbenturan dengan Route Laravel
RUN rm -f /app/public/robots.txt /app/public/sitemap.xml

# 7. Jalankan Artisan tasks & generate autoloader final
RUN COMPOSER_MEMORY_LIMIT=-1 composer dump-autoload --optimize --no-dev \
    && php artisan storage:link \
    && php artisan optimize \
    && php artisan octane:install --server=frankenphp

# 8. Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# 9. Jalankan Laravel Octane + FrankenPHP
EXPOSE 8000
CMD ["php", "artisan", "octane:start", "--workers=14", "--server=frankenphp", "--host=0.0.0.0", "--port=8000"]
