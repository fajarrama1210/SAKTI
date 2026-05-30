# 1. Gunakan PHP 8.4 karena Laravel/dependensi Anda mewajibkannya
FROM php:8.4-cli

# 2. Set working directory di dalam container
WORKDIR /app

# 3. Ambil mlocati/install-php-extensions untuk menangani instalasi ekstensi PHP secara otomatis
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# 4. Update package manager & install perkakas sistem (Git & Unzip wajib untuk Composer)
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
    && rm -rf /var/lib/apt/lists/*
    
# 5. Install ekstensi PHP yang dibutuhkan oleh Laravel & Octane secara aman
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

# 6. Salin seluruh kode aplikasi (termasuk composer.json yang sudah Anda tambahkan laravel/octane)
COPY --chown=www-data:www-data . /app

# 7. Salin konfigurasi php.ini kustom (Mendukung folder bernama 'Deploy' maupun 'deploy')
RUN cp /app/Deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini || cp /app/deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini

# 8. Ambil binary Composer resmi (Versi 2.2 sesuai konfigurasi awal Anda)
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# 9. Jalankan instalasi dependensi vendor secara bersih tanpa mengikutkan paket dev
RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
        --optimize-autoloader \
        --no-dev \
        --prefer-dist \
        --no-scripts \
        --ignore-platform-reqs \
    && rm -rf ~/.composer/cache

# 10. Hapus file statis yang berpotensi bentrok dengan sistem Route Laravel
RUN rm -f /app/public/robots.txt /app/public/sitemap.xml

# 11. Atur hak akses mutlak folder storage, cache, dan vendor agar dimiliki oleh www-data
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/vendor

# 12. Pindah ke user www-data demi keamanan container
USER www-data

# 13. Generate Autoloader final untuk mode produksi
RUN COMPOSER_MEMORY_LIMIT=-1 composer dump-autoload --optimize --no-dev --ignore-platform-reqs

# 14. Expose port yang digunakan oleh Laravel Octane
EXPOSE 8000

# 15. Jalankan optimasi Laravel dan startup Octane (FrankenPHP) dalam satu baris aman (Format JSON)
CMD ["sh", "-c", "php artisan storage:link || true && php artisan optimize && php artisan octane:start --workers=14 --server=frankenphp --host=0.0.0.0 --port=8000"]
