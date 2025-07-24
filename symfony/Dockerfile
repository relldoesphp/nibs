# Use a PHP-Apache image for Symfony, which includes Apache and PHP-FPM
FROM php:8.1-apache

# Install system dependencies required for common PHP extensions
RUN apt-get update && apt-get install -y git && rm -rf /var/lib/apt/lists/*
RUN apt-get update && apt-get install -y unzip && rm -rf /var/lib/apt/lists/*
RUN apt-get update && apt-get install -y libzip-dev && rm -rf /var/lib/apt/lists/*
RUN apt-get update && apt-get install -y libicu-dev && rm -rf /var/lib/apt/lists/*
RUN apt-get update && apt-get install -y libpng-dev && rm -rf /var/lib/apt/lists/*
RUN apt-get update && apt-get install -y libjpeg-dev && rm -rf /var/lib/apt/lists/*
RUN apt-get update && apt-get install -y libfreetype6-dev && rm -rf /var/lib/apt/lists/*


# Install Symfony CLI (optional for production, but fine for dev for convenience)
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony \
    && chmod +x /usr/local/bin/symfony

# Install PHP extensions commonly used by Symfony
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql zip intl opcache \
    && a2enmod rewrite \
    && a2enmod headers

# Install Composer globally in the container
COPY --from=composer/composer:latest /usr/bin/composer /usr/local/bin/composer

# Set the working directory inside the container to the Symfony project root
WORKDIR /var/www/html

# Copy your custom Apache config for Symfony
# This file will configure Apache to serve from public/ and listen on port 8000
COPY symfony.conf /etc/apache2/sites-available/000-default.conf
RUN a2ensite 000-default.conf # Enable your custom site configuration

# Disable the default Apache site that comes with the image to avoid conflicts
RUN a2dissite 000-default.conf

# We are relying on the host's vendor directory via bind mount.
# REMOVE THESE LINES:
# COPY composer.json composer.lock ./
# RUN composer install --no-dev --prefer-dist --optimize-autoloader

# Copy the rest of your application code (this will be overridden by bind mount for dev)
# This is good for ensuring the Dockerfile can build an image even without bind mounts
COPY . .

# Expose the port where Apache will listen inside the container
EXPOSE 8000

# The base 'php:X.Y-apache' image has a default CMD that starts Apache in the foreground.
# We rely on this default CMD.
