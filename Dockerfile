FROM php:8.2-cli

WORKDIR /app

# 1. Install system dependencies + Node.js (Added nodejs and npm)
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip libpng-dev libonig-dev gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 2. Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring zip

# 3. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Copy project
COPY . .

# 5. Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# 6. Build Frontend Assets (The missing step!)
RUN npm install
RUN npm run build

# 7. Laravel permissions fix
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=10000
