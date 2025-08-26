# Multi-stage build for production optimization
FROM php:8.3-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    icu-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    supervisor

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache

# Install Redis extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN addgroup -g 1000 www && adduser -u 1000 -G www -s /bin/sh -D www

# Set working directory
WORKDIR /var/www

# Copy application code
COPY --chown=www:www . .

# Development stage
FROM base AS development

# Install Xdebug with required headers
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del .build-deps

# Copy development PHP configuration
COPY docker/php/php-dev.ini /usr/local/etc/php/conf.d/custom.ini

# Install dependencies
RUN composer install --no-scripts --no-autoloader

# Generate autoloader
RUN composer dump-autoload --optimize

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000

CMD ["php-fpm"]

# Production stage
FROM base AS production

# Copy production PHP configuration
COPY docker/php/php-prod.ini /usr/local/etc/php/conf.d/custom.ini

# Install production dependencies only
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Generate autoloader
RUN composer dump-autoload --optimize --classmap-authoritative

# Set up Laravel caching
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

# Copy supervisor configuration
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create directories for supervisor and logs
RUN mkdir -p /var/log/supervisor

# Change ownership of storage and bootstrap/cache directories
RUN chown -R www:www /var/www/storage /var/www/bootstrap/cache

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
