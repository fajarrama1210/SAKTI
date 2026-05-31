# ✅ PERBAIKAN UTAMA: Gunakan image resmi FrankenPHP sebagai BASE IMAGE
# Ini memastikan semua library sistem (termasuk libwatcher-c.so.0) ikut tersedia
FROM dunglas/frankenphp:1-php8.4-bookworm

# 1. Set working directory
WORKDIR /app

# 2. Ambil mlocati/install-php-extensions untuk kemudahan install ekstensi PHP
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

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

# 5. Ambil Composer resmi
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# 6. Salin seluruh kode aplikasi
COPY --chown=www-data:www-data . /app

# 7. Salin konfigurasi php.ini kustom
RUN cp /app/Deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini || true

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

# 10. Atur hak akses folder storage, cache, vendor, DAN folder internal Caddy/FrankenPHP
# Folder /data dan /config dibutuhkan oleh Caddy untuk menyimpan konfigurasi & sertifikat
RUN mkdir -p /data/caddy /config/caddy \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/vendor /data /config

# 11. Pindah ke user www-data
USER www-data

# 12. Generate Autoloader final
RUN COMPOSER_MEMORY_LIMIT=-1 composer dump-autoload --optimize --no-dev --ignore-platform-reqs

# 13. Expose port Octane
EXPOSE 8000

# 14. RUNTIME COMMAND: Jalankan FrankenPHP via Octane
CMD ["sh", "-c", "rm -f bootstrap/cache/*.php && php artisan config:clear && php artisan route:clear && (php artisan storage:link || true) && OCTANE_SERVER=frankenphp php artisan optimize && php artisan octane:start --workers=4 --server=frankenphp --host=0.0.0.0 --port=8000"]
