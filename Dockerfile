FROM php:8.3-cli

# 1. Set working directory
WORKDIR /app

# 2. Ambil mlocati/install-php-extensions untuk menangani ekstensi PHP secara aman
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# 3. Update package manager & install perkakas sistem (Git & Unzip diperlukan oleh Composer)
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
    && rm -rf /var/lib/apt/lists/*
    
# 4. Install ekstensi PHP yang dibutuhkan aplikasi
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

# 5. Salin seluruh kode aplikasi terlebih dahulu agar folder Deploy ikut masuk ke container
COPY --chown=www-data:www-data . /app

# 6. Salin konfigurasi PHP kustom langsung dari dalam container (menghindari eror case-sensitive lokal)
RUN cp /app/Deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini || cp /app/deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini

# 7. Setup Composer & install dependencies
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# Jalankan instalasi dengan mengabaikan kecocokan platform ketat saat build demi stabilitas
RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
        --optimize-autoloader \
        --no-dev \
        --prefer-dist \
        --no-scripts \
        --ignore-platform-reqs \
    && rm -rf ~/.composer/cache

# 8. Hapus file statis yang berbenturan dengan Route Laravel
RUN rm -f /app/public/robots.txt /app/public/sitemap.xml

# 9. Jalankan Artisan tasks & generate autoloader final
RUN COMPOSER_MEMORY_LIMIT=-1 composer dump-autoload --optimize --no-dev \
    && php artisan storage:link \
    && php artisan optimize \
    && php artisan octane:install --server=frankenphp

# 10. Set permissions untuk storage dan cache Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# 11. Jalankan Laravel Octane + FrankenPHP
EXPOSE 8000
CMD ["php", "artisan", "octane:start", "--workers=14", "--server=frankenphp", "--host=0.0.0.0", "--port=8000"]
