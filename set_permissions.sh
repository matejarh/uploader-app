#!/bin/bash

# This script sets the correct permissions for a Laravel project
# It should be run from the root of the Laravel project

# Usage:
# Copy this file to the root of your Laravel project

# modify PROJECT_PATH to the path of your Laravel project

# Change the permissions
# chmod +x set_permissions.sh

# Run it
# ./set_permissions.sh


PROJECT_PATH="/var/www/laravel-project"

sudo chown -R www-data:www-data $PROJECT_PATH/storage $PROJECT_PATH/bootstrap/cache
sudo chmod -R 775 $PROJECT_PATH/storage $PROJECT_PATH/bootstrap/cache
sudo chown -R $USER:www-data $PROJECT_PATH
sudo chmod -R 755 $PROJECT_PATH
