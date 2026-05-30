FROM php:8.3-cli

# 1. Set working directory
WORKDIR /app

# 2. Install OS libs & PHP extensions dalam satu layer
#    - xmlwriter, xmlreader  -> dibutuhkan maatwebsite/excel
#    - bcmath               -> dibutuhkan aws-sdk & dompdf
#    - intl                 -> dibutuhkan Laravel/Carbon
#    - exif                 -> dibutuhkan manipulasi gambar
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libxml2-dev \
        libicu-dev \
        build-essential \
        nano \
        git \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        zip \
        gd \
        pcntl \
        opcache \
        pdo \
        pdo_mysql \
        bcmath \
        intl \
        exif \
        xmlwriter \
        xmlreader \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get purge -y --auto-remove build-essential \
    && rm -rf /var/lib/apt/lists/*

# 3. Copy konfigurasi PHP kustom
COPY ./Deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini

# 4. Copy hanya composer files dulu (layer caching agar tidak re-install jika kode berubah)
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./

# 5. Install PHP dependencies
RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
        --optimize-autoloader \
        --no-dev \
        --prefer-dist \
        --no-scripts \
    && rm -rf ~/.composer/cache

# 6. Salin seluruh kode aplikasi (setelah vendor ter-install)
COPY --chown=www-data:www-data . /app

# 7. Hapus file fisik robots/sitemap bawaan git agar tidak membentur Route Laravel
RUN rm -f /app/public/robots.txt /app/public/sitemap.xml

# 8. Jalankan Artisan tasks & generate autoloader ulang dengan script
RUN COMPOSER_MEMORY_LIMIT=-1 composer dump-autoload --optimize --no-dev \
    && php artisan storage:link \
    && php artisan optimize \
    && php artisan octane:install --server=frankenphp

# 9. Set permissions final untuk folder storage & cache
RUN chown -R www-data:www-data storage bootstrap/cache

# 10. Buka port dan nyalakan Laravel Octane + FrankenPHP
EXPOSE 8000
CMD ["php", "artisan", "octane:start", "--workers=14", "--server=frankenphp", "--host=0.0.0.0", "--port=8000"]
