### Step 1: Node.js for frontend (Vite)
FROM node:18 AS node-builder

WORKDIR /app
COPY . .

RUN npm install && npm run build

### Step 2: PHP for Laravel backend
FROM php:8.2-fpm

WORKDIR /var/www

# Install required packages
RUN apt-get update && apt-get install -y \
    zip unzip curl git libxml2-dev libzip-dev libpng-dev libjpeg-dev libonig-dev \
    sqlite3 libsqlite3-dev

# PHP Extensions
RUN docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl bcmath gd zip

# Copy Composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Laravel project files
COPY . /var/www
COPY --chown=www-data:www-data . /var/www

# Copy built frontend assets (from Vite)
COPY --from=node-builder /app/public/build /var/www/public/build

# Laravel setup
RUN composer install --no-dev --optimize-autoloader

# Copy .env file before running artisan commands
COPY .env.example .env

# Create database file (important for SQLite)
RUN touch database/database.sqlite

# Generate app key and run migrations
RUN php artisan key:generate && php artisan migrate --force

EXPOSE 8000
CMD php artisan serve --host=0.0.0.0 --port=8000
