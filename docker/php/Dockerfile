# Gunakan PHP 8.3 sesuai request composer.lock Anda sebelumnya
FROM php:8.3-fpm

# Install system dependencies
# Kita butuh 'libpq-dev' untuk Postgres
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
# INSTALL KEDUANYA: pdo_mysql DAN pdo_pgsql
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# Git safe directory (supaya tidak error ownership lagi)
RUN git config --global --add safe.directory /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . /var/www
COPY --chown=www-data:www-data . /var/www

USER www-data

EXPOSE 9000
CMD ["php-fpm"]