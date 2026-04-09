# ─── Stage 1: Node.js asset builder ───────────────────────────────────────────
FROM node:20-alpine AS node-builder

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY resources/ ./resources/
COPY vite.config.js ./
COPY public/ ./public/

RUN npm run build

# ─── Stage 2: PHP application ─────────────────────────────────────────────────
FROM php:8.3-apache AS app

# Install system dependencies + all libs needed for PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (gd with freetype + jpeg only — sufficient for QR code generation)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache modules and configure document root for Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && a2enmod rewrite headers

WORKDIR /var/www/html

# Install PHP dependencies first (better layer caching)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy full application
COPY . .

# Copy pre-built Vite assets from node stage
COPY --from=node-builder /app/public/build ./public/build

# Set correct ownership and permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Run Composer post-install hooks (package discovery etc.)
RUN composer run-script post-autoload-dump

# Use production PHP settings
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Copy and enable startup entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
