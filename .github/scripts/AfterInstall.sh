#!/bin/bash
find /var/www/laravel -type f -exec chmod 664 {} \;
find /var/www/laravel -type d -exec chmod 774 {} \;
chown -R nginx:nginx /var/www/laravel

chmod -R 775 /var/www/laravel/storage
chmod -R 775 /var/www/laravel/bootstrap/cache

# db migrate
# cd /var/www/laravel
# php artisan migrate

# composer
cd /var/www/laravel
composer install