FROM php:8.4-cli

# 1. Set working directory
WORKDIR /app

# 2. Ambil mlocati/install-php-extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# 3. Update & install perkakas sistem
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
    && rm -rf /var/lib/apt/lists/*
    
# 4. Install ekstensi PHP untuk Laravel & Octane
RUN install-php-extensions \
        gd \
        zip \
        pcntl \
        opcache \
        pdo_mysql \
        bcmath \
        intl \
        exif \
        xml \
        redis

# 5. Salin seluruh kode aplikasi
COPY --chown=www-data:www-data . /app

# 6. Salin konfigurasi php.ini kustom
RUN cp /app/Deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini || cp /app/deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini

# 7. Ambil Composer resmi
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# 8. Jalankan instalasi dependensi vendor (Sekarang aman pakai --no-dev karena Octane sudah diinstall di lokal)
RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
        --optimize-autoloader \
        --no-dev \
        --prefer-dist \
        --no-scripts \
        --ignore-platform-reqs \
    && rm -rf ~/.composer/cache

# 9. Hapus file statis yang berpotensi bentrok
RUN rm -f /app/public/robots.txt /app/public/sitemap.xml

# 10. Atur hak akses folder storage, cache, dan vendor
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/vendor

# 11. Pindah ke user www-data
USER www-data

# 12. Generate Autoloader final
RUN COMPOSER_MEMORY_LIMIT=-1 composer dump-autoload --optimize --no-dev --ignore-platform-reqs

# 13. Expose port Octane
EXPOSE 8000

# 14. Panggil shell script untuk menjalankan optimasi dan startup Octane secara aman (Format JSON)
CMD ["sh", "-c", "php artisan storage:link || true && php artisan optimize && php artisan octane:start --workers=14 --server=frankenphp --host=0.0.0.0 --port=8000"]
