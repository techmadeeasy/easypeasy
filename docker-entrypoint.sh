#!/bin/sh
set -e

# Install PHP dependencies
composer install --no-interaction --prefer-dist

# Run database migrations
php artisan migrate --force


# Start the web server using Laravel's built-in server
exec php artisan serve --host=0.0.0.0 --port=9000
