FROM php:8.3-cli-alpine

# Install system dependencies & PostgreSQL PHP extensions
RUN apk add --no-cache \
    postgresql-dev \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_pgsql pgsql bcmath pcntl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Expose port (default 8000)
EXPOSE 8000

# Set environment
ENV PORT=8000

# Start command: Run migrations and start Laravel server
CMD php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
