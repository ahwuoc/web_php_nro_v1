# Dockerfile
FROM php:8.2-apache

# Cài đặt dependencies cần thiết
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Cài đặt PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable mod_rewrite
RUN a2enmod rewrite

# Enable caching modules
RUN a2enmod cache cache_disk

# Enable compression
RUN a2enmod deflate

# Optimize Apache MPM Prefork
RUN echo "MaxRequestWorkers 256" >> /etc/apache2/mods-available/mpm_prefork.conf && \
    echo "StartServers 10" >> /etc/apache2/mods-available/mpm_prefork.conf && \
    echo "MinSpareServers 5" >> /etc/apache2/mods-available/mpm_prefork.conf && \
    echo "MaxSpareServers 20" >> /etc/apache2/mods-available/mpm_prefork.conf && \
    echo "MaxConnectionsPerChild 1000" >> /etc/apache2/mods-available/mpm_prefork.conf

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy source code
COPY . .

# Cài đặt dependencies
RUN rm -rf vendor composer.lock && composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80