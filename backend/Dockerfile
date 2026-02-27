FROM php:8.4-cli-alpine

# Install system dependencies
RUN apk add --no-cache \
    curl \
    git \
    unzip \
    sqlite-dev \
    oniguruma-dev \
    libxml2-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite mbstring xml pcntl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN mkdir -p /var/www/html/database/data && chown -R www-data:www-data /var/www/html

CMD ["php", "artisan", "serve", "--host", "0.0.0.0", "--port", "8005"]
