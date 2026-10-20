FROM php:8.2-cli-alpine

# Install system dependencies, MySQL/PDO extensions, and SSL certificates
RUN apk add --no-cache \
    curl \
    git \
    unzip \
    libzip-dev \
    ca-certificates \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy project files
COPY . .

# Install dependencies (ignoring scripts during image build)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set directory permissions for Laravel storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy entrypoint script and make sure it is executable
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]