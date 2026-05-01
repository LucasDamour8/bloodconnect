FROM php:8.2-cli

WORKDIR /app

# 1. Install system dependencies + Node.js 20
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip libpng-dev libonig-dev gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 2. Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring zip

# 3. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Copy all project files
COPY . .

# 5. Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# 6. Build Frontend Assets (Fixes the missing styles)
RUN npm install
RUN npm run build

# 7. Create the storage link (Fixes missing images)
RUN php artisan storage:link

# 8. Permissions & Ownership fix (Fixes 500 errors on Register/Appointments)
RUN chmod -R 775 storage bootstrap/cache public
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

EXPOSE 10000

# 9. Start the application
CMD php artisan serve --host=0.0.0.0 --port=10000
