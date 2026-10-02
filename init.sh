#!/bin/bash

# Navigate to the Laravel project directory
cd /home/pravasmer/nalaganje.prava-smer.si/

# Generate a new application key
php artisan key:generate
echo "New application key generated and fresh migrations run successfully."

# Run fresh migrations
php artisan migrate:fresh
php artisan db:seed


# Clear the application cache
php artisan cache:clear

# Clear the configuration cache
php artisan config:clear

# Clear the route cache
php artisan route:clear

# Clear the view cache
php artisan view:clear

echo "Caches cleared successfully."
