FROM php:8.3-fpm

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
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

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Create non-root user
RUN useradd -u 1000 -m laravel

WORKDIR /var/www

# Copy application source
COPY --chown=laravel:laravel . .

# Set ownership untuk direktori /var/www dan subdirektori penting
RUN chown -R laravel:laravel /var/www \
 && chmod -R 755 /var/www \
 && chmod -R 775 storage bootstrap/cache

USER laravel

EXPOSE 9000
CMD ["php-fpm"]


# 1 Menambahkan `chown -R laravel:laravel /var/www`** - Ini yang paling penting! Memastikan direktori utama `/var/www` juga dimiliki oleh user `laravel`, bukan hanya isinya.

# 2.Menambahkan `chmod -R 755 /var/www`** - Memastikan permission yang tepat untuk seluruh direktori.

# 3.chmod -R 775 storage bootstrap/cache`** - Memberikan write permission ekstra untuk direktori yang sering ditulis Laravel.

# **Urutan Eksekusi yang Benar:** 
# WORKDIR /var/www          # Dibuat sebagai root
# COPY --chown ...          # File-file dicopy dengan owner laravel
# RUN chown -R laravel ...  # PENTING: Direktori /var/www dan semua isinya jadi milik laravel
# USER laravel              # Switch ke user laravel