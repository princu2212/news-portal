FROM php:8.2-cli-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite-dev \
    nodejs \
    npm

RUN docker-php-ext-install pdo pdo_sqlite bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install frontend dependencies and build assets
RUN npm ci || npm install
RUN npm run build

# Setup permissions
RUN chmod -R 777 storage bootstrap/cache
RUN chmod +x docker-entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/var/www/docker-entrypoint.sh"]
