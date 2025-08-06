# ---- Base PHP Stage ----
FROM php:8.2-fpm as base
WORKDIR /var/www

# Install base dependencies
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

# ---- Composer Stage ----
FROM base as composer_stage
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist

# ---- Application Build Stage ----
FROM base as build_stage
COPY --from=composer_stage /var/www/vendor /var/www/vendor
COPY . .
RUN php -r "file_exists('.env') || copy('.env.example', '.env');" \
    && php artisan key:generate

# ---- Final Production Stage ----
FROM php:8.2-fpm-alpine as production_stage
WORKDIR /var/www

# Install only necessary production dependencies
RUN apk --no-cache add nginx supervisor

# Copy application files from build stage
COPY --from=build_stage /var/www /var/www

# Copy Nginx and Supervisor configurations
COPY docker/default.conf /etc/nginx/http.d/default.conf

# Copy the start-up script
COPY docker/start-services.sh /usr/local/bin/start-services.sh
RUN chmod +x /usr/local/bin/start-services.sh

# Create Supervisor config
RUN echo '[supervisord]' > /etc/supervisord.conf && \
    echo 'nodaemon=true' >> /etc/supervisord.conf && \
    echo '' >> /etc/supervisord.conf && \
    echo '[program:nginx]' >> /etc/supervisord.conf && \
    echo 'command=/usr/sbin/nginx -g "daemon off;"' >> /etc/supervisord.conf && \
    echo '' >> /etc/supervisord.conf && \
    echo '[program:php-fpm]' >> /etc/supervisord.conf && \
    echo 'command=/usr/local/sbin/php-fpm' >> /etc/supervisord.conf

# Set permissions
RUN chown -R www-data:www-data /var/www && \
    chmod -R 775 /var/www/storage

EXPOSE 80

CMD ["/usr/local/bin/start-services.sh"]