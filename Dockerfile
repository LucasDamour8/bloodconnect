FROM php:8.2-cli

WORKDIR /app

# 1. Install system dependencies + Node.js 20
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip libpng-dev libonig-dev gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 2. Install PHP extensions (Added bcmath)
RUN docker-php-ext-install pdo pdo_mysql mbstring zip bcmath

# 3. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Copy project files
COPY . .

# 5. Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# 6. Build Frontend Assets
RUN npm install && npm run build

# 7. Setup Storage and Permissions
# We ensure the www-data user owns the files before starting
RUN php artisan storage:link
RUN chown -R www-data:www-data /app && chmod -R 775 /app/storage /app/bootstrap/cache

EXPOSE 10000

# 8. Start Command
# Added config:cache to make sure the APP_KEY from Render is loaded
CMD php artisan config:cache && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000
