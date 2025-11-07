# Gunakan PHP versi 8.2 agar cocok dengan composer.json
FROM php:8.2-cli

# Instal dependency sistem untuk GD, PDO, dan lain-lain
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    libzip-dev \
    libonig-dev \
    zlib1g-dev \
    unzip \
    git \
    && docker-php-ext-configure gd \
        --with-jpeg \
        --with-freetype \
        --with-webp \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Set working directory
WORKDIR /var/www/html

# Salin semua file ke container
COPY . .

# Install dependencies Laravel
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Cache konfigurasi Laravel
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

# Jalankan Laravel di port Railway
CMD php artisan serve --host=0.0.0.0 --port=$PORT
