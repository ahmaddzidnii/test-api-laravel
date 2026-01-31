# =========================
# Stage 1: Build dependency
# =========================
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# =========================
# Stage 2: Runtime
# =========================
FROM php:8.3-fpm

# Install system & PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
 && docker-php-ext-install \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*

# Create non-root user
RUN useradd -u 1000 -m laravel

WORKDIR /var/www

# Copy application source
COPY . .

# Copy vendor from build stage
COPY --from=vendor /app/vendor ./vendor

# Set permission for Laravel writable dirs
RUN chown -R laravel:laravel \
    storage \
    bootstrap/cache

USER laravel

EXPOSE 9000
CMD ["php-fpm"]
