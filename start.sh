#!/bin/bash
set -e

echo "Running migrations..."
php artisan migrate --force

# Clear active games since microservice restarts fresh and loses in-memory game state.
# This prevents 502 errors when seeking with stale game data.
echo "Clearing stale active games..."
php artisan db:seed --class='Database\Seeders\ClearActiveGamesSeeder' --force 2>/dev/null || \
    php -r "DB::table('games')->where('status', 'active')->update(['status' => 'abandoned', 'termination' => 'microservice_restart']);"

# echo "Seeding puzzles..."
# php artisan db:seed --class=PuzzleSeeder --force

echo "Clearing caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Apache..."
exec apache2-foreground
