#!/bin/bash
set -e

# echo "Running migrations..."
# php artisan migrate --force

# # # Grant admin access to vonchess.official@gmail.com
# # echo "Granting admin access..."
# # php artisan tinker --execute="App\Models\User::where('email', 'vonchess.official@gmail.com')->update(['is_admin' => true]);"

# # # Clear active games since microservice restarts fresh
# # echo "Clearing stale active games..."
# # php artisan tinker --execute="DB::table('games')->where('status', 'active')->update(['status' => 'abandoned', 'termination' => 'microservice_restart']);"

# # # Refresh puzzle database for a clean slate with 10k items
# # echo "Refreshing puzzle database..."
# # php artisan tinker --execute="DB::table('puzzles')->truncate();"
# # php artisan db:seed --class=PuzzleSeeder --force

echo "Clearing caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Apache..."
exec apache2-foreground
