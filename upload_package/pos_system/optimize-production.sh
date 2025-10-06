#!/bin/bash

echo "🚀 Optimizing Laravel Application for Production..."

# Clear all caches first
echo "📝 Clearing existing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generate application key if not exists
echo "🔑 Generating application key..."
php artisan key:generate --force

# Cache configurations for better performance
echo "⚡ Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize Composer autoloader
echo "📦 Optimizing Composer autoloader..."
composer install --optimize-autoloader --no-dev

# Create symbolic link for storage
echo "🔗 Creating storage symbolic link..."
php artisan storage:link

echo "✅ Laravel optimization completed!"
echo ""
echo "📋 Next steps for deployment:"
echo "1. Upload all files to your hosting provider"
echo "2. Update .env file with production settings"
echo "3. Run database migrations: php artisan migrate --force"
echo "4. Set proper file permissions (755 for directories, 644 for files)"
echo "5. Point your domain to the 'public' directory"