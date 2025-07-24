#!/bin/bash
# drupal/startup.sh

# Go to the Drupal web root for Composer and Drush commands
cd /var/www/html/web

## Wait for the database to be ready using wait-for-it.sh
#echo "Waiting for drupal_db:3306 to be ready..."
## wait-for-it.sh usage: wait-for-it.sh host:port -t timeout -- command_to_run_after_ready
#/usr/local/bin/wait-for-it.sh drupal_db:3306 -t 120 -- echo "drupal_db is ready!"
#
## Check if wait-for-it.sh succeeded
#if [ $? -ne 0 ]; then
#  echo "Error: drupal_db was not ready within the timeout. Exiting."
#  exit 1
#fi

# Ensure vendor directory exists and dependencies are installed
# This is crucial because the base image might not have all dev dependencies
# or if your local 'web' directory is empty initially.
if [ ! -d vendor ]; then
  echo "Composer vendor directory not found. Running composer install..."
  composer install --no-dev --prefer-dist --optimize-autoloader
  echo "Composer dependencies installed."
else
  echo "Composer vendor directory found. Skipping composer install."
fi

# Ensure correct permissions for settings.php and files directory
# These permissions are critical for Drupal to function
echo "Setting permissions for sites/default..."
mkdir -p sites/default/files
# Copy default.settings.php if settings.php doesn't exist
cp -n sites/default/default.settings.php sites/default/settings.php
# Set permissions for files and settings.php
chmod -R 777 sites/default/files # Allow web server to write to files
chmod 644 sites/default/settings.php # Secure settings.php
chmod 777 sites/default # Allow creation of settings.php and files directory

echo "Permissions set."

# Check if Drupal is installed via settings.php or if drush can bootstrap
# The drush status check is more robust than just checking settings.php
if [ ! -f sites/default/settings.php ] || ! drush status --field=bootstrap | grep -q 'Successful'; then
  echo "Drupal site not found or not bootstrapped. Starting installation..."
  # Ensure these credentials match your docker-compose.yml for drupal_db
  drush site:install standard \
    --db-url=mysql://nibs_user:tyrellwillbeagreatemployee@drupal_db:3306/nibs_drupal \
    --site-name='NIBS Intranet Portal' \
    --account-name=admin \
    --account-pass=adminpassword \
    --account-mail=admin@example.com \
    --yes
  echo 'Drupal site installed successfully.'
else
  echo 'Drupal site already installed. Skipping installation.'
fi

# Go back to the Apache default working directory (if needed, but usually not for drupal:apache images)
# cd /var/www/html

# Start Apache in the foreground (this is the default CMD of drupal:apache images)
# We need to explicitly call it here because our startup.sh is the main CMD.
echo "Starting Apache..."
apache2-foreground

