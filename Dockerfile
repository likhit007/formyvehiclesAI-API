FROM php:8.2-cli-alpine

# Install system libraries and build tools
RUN apk add --no-cache \
    curl \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libxml2-dev \
    oniguruma-dev \
    ca-certificates \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        zip \
        bcmath \
        ctype \
        fileinfo \
        mbstring \
        xml

# Install Composer binary from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy all application files
COPY . .

# Run Composer with --no-scripts to prevent artisan discovery during image build
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Set permissions for Laravel cache and storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy the entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]