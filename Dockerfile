# syntax=docker/dockerfile:1.4

# ============================================
# Stage: base
# Common base for all environments
# ============================================
FROM php:8.4-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    git \
    unzip \
    zip \
    curl \
    bash \
    icu-dev \
    libzip-dev \
    postgresql-dev

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    intl \
    opcache \
    zip

# APCu for Ganesha Circuit Breaker (no Redis required)
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && apk del $PHPIZE_DEPS

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# ============================================
# Stage: dependencies
# Install Composer dependencies
# ============================================
FROM base AS dependencies

# Copy composer files first (better layer caching)
COPY composer.json composer.lock ./

# Install dependencies (dev mode)
RUN composer install --prefer-dist --no-scripts --no-autoloader

# Copy application files
COPY . .

# Generate autoload files
RUN composer dump-autoload --optimize

# ============================================
# Stage: development
# For local and develop environments
# ============================================
FROM dependencies AS development

# Install Xdebug
RUN apk add --no-cache $PHPIZE_DEPS linux-headers \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

# Xdebug configuration
RUN echo "xdebug.mode=debug,coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Development PHP settings
COPY docker/php/php-dev.ini /usr/local/etc/php/conf.d/php-dev.ini

# Set permissions (development only - relaxed permissions)
RUN chown -R www-data:www-data /app

USER www-data

EXPOSE 9000

CMD ["php-fpm"]
