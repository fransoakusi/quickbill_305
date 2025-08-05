FROM php:8.2-apache
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mysqli \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Enable Apache modules
RUN a2enmod rewrite headers

# Copy application files
COPY . /var/www/html/

# Create directories and set permissions
RUN mkdir -p uploads/businesses uploads/properties uploads/receipts uploads/bills uploads/imports uploads/temp \
    && mkdir -p storage/logs storage/backups storage/exports storage/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 uploads storage

EXPOSE 80
CMD ["apache2-foreground"]
