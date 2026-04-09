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

# Install ALL system dependencies needed by our PHP extensions:
#   libpng-dev        → gd (PNG support)
#   libfreetype6-dev  → gd (font rendering)
#   libjpeg-dev       → gd (JPEG support) — more portable than libjpeg62-turbo-dev
#   libonig-dev       → mbstring
#   libzip-dev        → zip
#   libicu-dev        → intl (this was the missing dep in previous attempts)
#   libxml2-dev       → xml / soap
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpng-dev \
    libfreetype6-dev \
    libjpeg-dev \
    libonig-dev \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configure GD with freetype + jpeg, then install all extensions in one pass
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

# Enable Apache rewrite + point document root to Laravel public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && a2enmod rewrite headers

# Fix "More than one MPM loaded" — php:8.3-apache compiles mpm_event, mpm_worker, and
# mpm_prefork as STATIC modules, so a2dismod and .load symlinks have no effect on them.
# Apache decides which MPM is active based solely on which mpm_*.conf files are present
# in mods-enabled. We therefore:
#   1. Wipe every mpm_*.conf and mpm_*.load entry from mods-enabled (belt + suspenders).
#   2. Write a clean mpm_prefork.conf into mods-available (overwrite whatever was there).
#   3. Symlink only that one conf into mods-enabled — no .load file needed for static MPMs.
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load \
          /etc/apache2/mods-enabled/mpm_*.conf \
    && printf '<IfModule mpm_prefork_module>\n\
    StartServers             5\n\
    MinSpareServers          5\n\
    MaxSpareServers         10\n\
    MaxRequestWorkers      150\n\
    MaxConnectionsPerChild   0\n\
</IfModule>\n' > /etc/apache2/mods-available/mpm_prefork.conf \
    && ln -s /etc/apache2/mods-available/mpm_prefork.conf \
             /etc/apache2/mods-enabled/mpm_prefork.conf

WORKDIR /var/www/html

# Install PHP deps first for layer caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy full app source
COPY . .

# Overlay pre-built Vite assets
COPY --from=node-builder /app/public/build ./public/build

# Fix permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Run Composer post-install hooks
RUN composer run-script post-autoload-dump

# Use production PHP settings
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Startup entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
