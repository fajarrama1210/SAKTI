# =============================================================================
# Dockerfile Final: PHP 8.4 + Laravel 12 + Octane + FrankenPHP
# Optimized for Dockploy / Production - FIXED VERSION
# =============================================================================

FROM php:8.4-cli

ENV DEBIAN_FRONTEND=noninteractive \
    COMPOSER_VERSION=2.8 \
    COMPOSER_MEMORY_LIMIT=-1 \
    APP_ENV=production \
    APP_DEBUG=false

WORKDIR /app

# Install extension helper
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        curl \
        ca-certificates \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
    && install-php-extensions \
        @composer \
        bcmath \
        bz2 \
        curl \
        exif \
        fileinfo \
        gd \
        intl \
        mbstring \
        opcache \
        pcntl \
        pdo_mysql \
        redis \
        simplexml \
        tokenizer \
        xml \
        xmlreader \
        xmlwriter \
        zip \
        openssl \
    && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Composer install dengan layer caching
COPY composer.json composer.lock ./

RUN composer install \
        --optimize-autoloader \
        --no-dev \
        --prefer-dist \
        --no-interaction \
        --no-progress \
        --no-scripts \
    && rm -rf ~/.composer/cache

# Copy source code & konfigurasi
COPY --chown=www-data:www-data . /app
COPY ./deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini

# Pre-setup folder & permissions
RUN mkdir -p /var/www/.octane /app/storage /app/bootstrap/cache \
    && chown -R www-data:www-data /var/www/.octane /app/storage /app/bootstrap/cache /app/vendor

# Laravel optimization (build time)
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan event:cache \
    && (php artisan storage:link || true) \
    && chown -R www-data:www-data /app/storage

# Security: non-root user
USER www-data

EXPOSE 8000

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD curl -f http://localhost:8000/api/health || exit 1

CMD ["php", "artisan", "octane:start", \
     "--server=frankenphp", \
     "--host=0.0.0.0", \
     "--port=8000", \
     "--workers=14", \
     "--max-requests=500"]
