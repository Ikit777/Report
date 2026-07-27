#!/bin/bash

echo "=== Clearing All Laravel Cache ==="

echo "1. Clearing application cache..."
php artisan cache:clear

echo "2. Clearing view cache..."
php artisan view:clear

echo "3. Clearing config cache..."
php artisan config:clear

echo "4. Clearing route cache..."
php artisan route:clear

echo "5. Clearing compiled classes..."
php artisan clear-compiled

echo ""
echo "=== All cache cleared successfully! ==="
echo "Now refresh your browser and try creating/editing a report."
