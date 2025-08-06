# Use a specific PHP version. Render recommends -fpm variants.
FROM php:8.2-fpm

# Set the working directory
WORKDIR /var/www

# Install system dependencies required by Laravel and common extensions
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    libpq-dev \
    libonig-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the application source code
COPY . .

# Render will inject environment variables, but we need a file for artisan commands during the build.
# This command copies .env.example to .env if .env does not exist.
RUN php -r "file_exists('.env') || copy('.env.example', '.env');"

# Install composer dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Generate an application key
RUN php artisan key:generate

# Set permissions for storage and cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose port 8000 for the web server
EXPOSE 8000

# The command to run when the container starts
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]