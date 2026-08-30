FROM php:8.4-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    curl \
    libpq-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    linux-headers \
    $PHPIZE_DEPS

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    intl \
    bcmath \
    zip \
    opcache

# Install Redis extension via PECL
RUN pecl install redis && docker-php-ext-enable redis

# Clean up build dependencies
RUN apk del $PHPIZE_DEPS

# Install Composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Create non-root user and setup permissions
RUN chown -R www-data:www-data /var/www/html

USER www-data

EXPOSE 9000

CMD ["php-fpm"]
