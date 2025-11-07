# Gunakan PHP versi 8.2
FROM php:8.2-cli

# Instal dependency sistem untuk GD, PDO, Zip, dll.
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
    curl \
    && docker-php-ext-configure gd \
        --with-jpeg \
        --with-freetype \
        --with-webp \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Set working directory
WORKDIR /var/www/html

# Salin file proyek
COPY . .

# Install dependencies tanpa menjalankan artisan command di tahap build
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Jalankan Laravel saat container aktif (runtime)
CMD php artisan config:clear \
 && php artisan view:clear \
 && php artisan route:clear \
 && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
