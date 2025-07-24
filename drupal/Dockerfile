# Use the official Drupal 10 Apache image as the base
FROM drupal:10-apache

# Install Drush via Composer and essential system utilities
RUN apt-get update && apt-get install -y \
    git \
    netcat-traditional \ # Provides 'nc' for wait-for-it.sh
    unzip \
    && rm -rf /var/lib/apt/lists/* # Clean up apt cache

# Install Composer globally in the container
COPY --from=composer/composer:latest /usr/bin/composer /usr/bin/composer

# Install Drush globally in the container
RUN composer global require drush/drush \
    && ln -s /root/.composer/vendor/bin/drush /usr/local/bin/drush \
    && chmod +x /usr/local/bin/drush

# Copy the startup script and make it executable
COPY startup.sh /usr/local/bin/startup.sh
RUN chmod +x /usr/local/bin/startup.sh

# Copy the wait-for-it.sh script and make it executable
COPY wait-for-it.sh /usr/local/bin/wait-for-it.sh
RUN chmod +x /usr/local/bin/wait-for-it.sh

# The base 'drupal:10-apache' image already sets WORKDIR /var/www/html
# and handles Apache's default CMD.
# Our startup.sh will handle Drupal installation and then start Apache.

EXPOSE 80

