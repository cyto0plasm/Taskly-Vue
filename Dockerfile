FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    zip \
    unzip \
    git \
    nodejs \
    npm \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite

# Install Composer
COPY --from=composer:latest /usr/local/bin/composer /usr/local/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install JS dependencies and build
RUN npm install && npm run build

# Set permissions
RUN chmod -R 775 storage bootstrap/cache
RUN touch database/database.sqlite

# Expose port
EXPOSE 10000

# Start
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000
