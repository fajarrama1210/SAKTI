FROM php:8.3-cli

# 1. Set working directory
WORKDIR /app

# Menyalin seluruh kode aplikasi ke dalam container
COPY --chown=www-data:www-data . /app

# 2. Hapus file fisik robots/sitemap bawaan git agar tidak membentur Route Laravel
RUN rm -f /app/public/robots.txt /app/public/sitemap.xml

# 3. Install OS libs, build tools & PHP extensions dalam satu layer (Efisiensi Cache)
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    build-essential nano git unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) zip gd pcntl opcache pdo pdo_mysql \
    && pecl install redis \
    && docker-php-ext-enable redis \
    # Hapus kembali build dependencies untuk memperkecil ukuran image
    && apt-get purge -y --auto-remove build-essential libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# 4. Copy konfigurasi PHP kustom
COPY ./Deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini

# 5. Install PHP dependencies lewat Composer menggunakan image resmi Composer
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer
RUN composer install --optimize-autoloader --no-dev --prefer-dist \
    && rm -rf ~/.composer/cache

# 6. Jalankan Artisan tasks & Inisialisasi FrankenPHP
RUN php artisan storage:link \
    && php artisan optimize \
    && php artisan octane:install --server=frankenphp

# 7. Set permissions final untuk folder storage & cache
RUN chown -R www-data:www-data storage bootstrap/cache

# 8. Buka port dan nyalakan Laravel Octane + FrankenPHP
EXPOSE 8000
CMD ["php", "artisan", "octane:start", "--workers=14", "--server=frankenphp", "--host=0.0.0.0", "--port=8000"]
