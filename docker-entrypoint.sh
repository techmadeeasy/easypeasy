#!/bin/sh
set -e

# Copy environment file if it does not exist
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Install PHP dependencies
composer install --no-interaction --prefer-dist

# Generate application key if not already set
php artisan key:generate --force

# Install Node dependencies
npm install

# Build front-end assets
npm run build

# Run database migrations
php artisan migrate --force


# Start the web server using Laravel's built-in server
exec php artisan serve --host=0.0.0.0 --port=9000
