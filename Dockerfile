FROM php:8.4-cli

# 1. Set working directory
WORKDIR /app

# 2. Ambil mlocati/install-php-extensions dan frankenphp binary resmi
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=dunglas/frankenphp:1-php8.4-bookworm /usr/local/bin/frankenphp /usr/local/bin/frankenphp

# 3. Update & install perkakas sistem
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
    && rm -rf /var/lib/apt/lists/*
    
# 4. Install seluruh ekstensi PHP yang diwajibkan Laravel & Octane
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

# 5. Salin seluruh kode aplikasi (Ini akan membawa composer.json & composer.lock terbaru Anda)
COPY --chown=www-data:www-data . /app

# 6. Salin konfigurasi php.ini kustom
RUN cp /app/Deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini || cp /app/Deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini

# 7. Ambil Composer resmi
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# 8. Jalankan instalasi dependensi vendor secara bersih
RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
        --optimize-autoloader \
        --no-dev \
        --prefer-dist \
        --no-scripts \
        --ignore-platform-reqs \
    && rm -rf ~/.composer/cache

# 9. Hapus file statis yang berpotensi bentrok dengan route
RUN rm -f /app/public/robots.txt /app/public/sitemap.xml

# 10. Atur hak akses mutlak folder storage, cache, dan vendor agar dimiliki www-data
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/vendor

# 11. Pindah ke user www-data
USER www-data

# 12. Generate Autoloader final
RUN COMPOSER_MEMORY_LIMIT=-1 composer dump-autoload --optimize --no-dev --ignore-platform-reqs

# 13. Expose port Octane
EXPOSE 8000

# 14. RUNTIME COMMAND: Bersihkan cache lama terlebih dahulu sebelum mengoptimasi ulang saat container menyala
# 14. RUNTIME COMMAND: Diberikan tanda kurung ( ) pada storage:link agar tidak merusak rantai eksekusi Octane
# 14. PAKSA registrasi Octane dengan menginjeksikan OCTANE_SERVER langsung di runtime command
CMD ["sh", "-c", "rm -f bootstrap/cache/*.php && php artisan config:clear && php artisan route:clear && (php artisan storage:link || true) && OCTANE_SERVER=frankenphp php artisan optimize && php artisan octane:start --workers=14 --server=frankenphp --host=0.0.0.0 --port=8000"]
