# =============================================================================
# Dockerfile Final: PHP 8.4 + Laravel 12 + Octane + FrankenPHP
# Optimized for Dockploy / Production
# =============================================================================

FROM php:8.4-cli

# 1. Environment Variables (Wajib untuk build stabil)
ENV DEBIAN_FRONTEND=noninteractive \
    COMPOSER_VERSION=2.8 \
    COMPOSER_MEMORY_LIMIT=-1 \
    OCTANE_FRANKENPHP_VERSION=latest \
    APP_ENV=production \
    APP_DEBUG=false

# 2. Set Working Directory
WORKDIR /app

# 3. Install System Dependencies & PHP Extensions Helper
# Menggunakan mlocati/php-extension-installer agar instalasi ekstensi PHP 8.4 lebih stabil
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        curl \
        ca-certificates \
        libonig-dev \          # Wajib untuk maatwebsite/excel (mbstring dependency)
        libxml2-dev \          # Wajib untuk simplexml/dom
        libzip-dev \           # Wajib untuk zip extension
        libpng-dev \           # Wajib untuk gd
        libjpeg-dev \          # Wajib untuk gd
        libfreetype6-dev \     # Wajib untuk gd
    # Install semua ekstensi PHP yang dibutuhkan Laravel 12 + Paket kamu
    && install-php-extensions \
        @composer \            # Install composer langsung via helper (lebih aman) \
        bcmath \
        bz2 \
        curl \
        exif \
        fileinfo \             # KRITIS: Sering missing, wajib untuk Laravel core \
        gd \
        intl \
        mbstring \
        opcache \
        pcntl \
        pdo_mysql \
        redis \
        simplexml \            # KRITIS: Wajib untuk maatwebsite/excel \
        tokenizer \
        xml \
        xmlreader \
        xmlwriter \
        zip \
        openssl \
    # Cleanup system packages to reduce image size
    && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# 4. Composer Install (Layer Caching - Agar build cepat & stabil)
# Copy hanya file dependency dulu, biar layer ini tidak rebuild saat kode berubah
COPY composer.json composer.lock ./

RUN composer install \
        --optimize-autoloader \
        --no-dev \
        --prefer-dist \
        --no-interaction \
        --no-progress \
        --no-scripts \         # Skip scripts saat install (jalankan manual nanti) \
    && rm -rf ~/.composer/cache

# 5. Copy Source Code & Konfigurasi
# Copy seluruh aplikasi
COPY --chown=www-data:www-data . /app

# Copy php.ini production
COPY ./Deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini

# 6. Pre-Setup Folder & Permissions (Mencegah Error Runtime)
# Buat folder untuk binary FrankenPHP agar www-data bisa write saat pertama kali run
RUN mkdir -p /var/www/.octane /app/storage /app/bootstrap/cache \
    && chown -R www-data:www-data /var/www/.octane /app/storage /app/bootstrap/cache /app/vendor

# 7. Laravel Optimization (Build Time)
# Jalankan optimize SEKARANG saat build, bukan saat container start
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan event:cache \
    # Storage link aman (ignore error jika sudah ada)
    && (php artisan storage:link || true) \
    && chown -R www-data:www-data /app/storage

# 8. Security: Switch to Non-Root User
USER www-data

# 9. Expose Port
EXPOSE 8000

# 10. Healthcheck (Opsional tapi bagus untuk Dockploy)
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD curl -f http://localhost:8000/api/health || exit 1

# 11. Start Command (Octane + FrankenPHP)
# Menggunakan exec form (JSON array) agar signal handling (SIGTERM) bekerja benar
CMD ["php", "artisan", "octane:start", \
     "--server=frankenphp", \
     "--host=0.0.0.0", \
     "--port=8000", \
     "--workers=14", \
     "--max-requests=500"]
