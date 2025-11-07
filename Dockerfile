FROM php:8.3-cli

# Install ekstensi yang dibutuhkan Laravel
RUN docker-php-ext-install pdo pdo_mysql gd

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

CMD php artisan serve --host=0.0.0.0 --port=$PORT
