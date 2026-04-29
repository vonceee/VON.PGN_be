#!/bin/bash
set -e

echo "Running migrations..."
# Retry migrations up to 3 times if they fail due to connection timeouts
for i in {1..3}; do
    php artisan migrate --force && break || {
        if [ $i -lt 3 ]; then
            echo "Migration failed, retrying in 5 seconds... ($i/3)"
            sleep 5
        else
            echo "Migration failed after 3 attempts. Proceeding to start server anyway..."
        fi
    }
done

echo "Clearing caches..."
php artisan config:cache
php artisan view:cache

echo "Starting Apache..."
exec apache2-foreground
