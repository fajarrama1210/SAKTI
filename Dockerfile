FROM php:8.3-cli

# 1. Set working directory di dalam container
WORKDIR /app

# 2. Ambil mlocati/install-php-extensions untuk menangani instalasi ekstensi PHP secara otomatis
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# 3. Update package manager & install perkakas sistem (Git & Unzip wajib untuk Composer)
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
    && rm -rf /var/lib/apt/lists/*
    
# 4. Install ekstensi PHP yang dibutuhkan oleh Laravel / Lumen & Octane
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

# 5. Salin seluruh kode aplikasi terlebih dahulu agar folder konfigurasi ikut masuk
COPY --chown=www-data:www-data . /app

# 6. Salin konfigurasi PHP kustom (Mendukung folder bernama 'Deploy' maupun 'deploy')
RUN cp /app/Deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini || cp /app/deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini

# 7. Ambil binary Composer resmi (Versi 2.2 sesuai struktur awal Anda)
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# 8. Jalankan instalasi dependensi via Composer
#    Menggunakan --ignore-platform-reqs agar build tidak gagal akibat validasi ketat environment
RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
        --optimize-autoloader \
        --no-dev \
        --prefer-dist \
        --no-scripts \
        --ignore-platform-reqs \
    && rm -rf ~/.composer/cache

# 9. Hapus file statis yang berpotensi bentrok dengan Route Laravel
RUN rm -f /app/public/robots.txt /app/public/sitemap.xml

RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/vendor

# 11. Tetap gunakan user root terlebih dahulu untuk menjalankan Composer dump-autoload & Artisan
#     (Atau bisa dijalankan sekarang karena izin folder vendor sudah dibuka di atas)
USER www-data

# 12. Generate Autoloader final & lakukan optimasi Laravel
RUN COMPOSER_MEMORY_LIMIT=-1 composer dump-autoload --optimize --no-dev

# Menggunakan DB Dummy (SQLite in-memory) agar Laravel tidak eror saat mencoba terhubung ke DB asli sewaktu build
RUN DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan storage:link || true
RUN DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan optimize

# 13. Expose port yang digunakan oleh Laravel Octane
EXPOSE 8000

# 14. Jalankan Laravel Octane dengan server FrankenPHP saat container dinyalakan
CMD ["php", "artisan", "octane:start", "--workers=14", "--server=frankenphp", "--host=0.0.0.0", "--port=8000"]
