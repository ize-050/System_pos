#!/bin/bash

echo "📦 Preparing files for shared hosting upload..."

# Create upload directory
mkdir -p upload_package

# Copy main application files (excluding unnecessary files)
echo "📁 Copying application files..."
rsync -av --exclude='node_modules' \
          --exclude='.git' \
          --exclude='storage/logs/*' \
          --exclude='storage/framework/cache/*' \
          --exclude='storage/framework/sessions/*' \
          --exclude='storage/framework/views/*' \
          --exclude='tests' \
          --exclude='.env' \
          --exclude='upload_package' \
          . upload_package/pos_system/

# Copy public files to public_html directory
echo "🌐 Preparing public_html files..."
mkdir -p upload_package/public_html
cp -r public/css upload_package/public_html/ 2>/dev/null || true
cp -r public/js upload_package/public_html/ 2>/dev/null || true
cp -r public/build upload_package/public_html/ 2>/dev/null || true
cp public/favicon.ico upload_package/public_html/ 2>/dev/null || true
cp public/robots.txt upload_package/public_html/ 2>/dev/null || true
cp public/.htaccess upload_package/public_html/ 2>/dev/null || true

# Copy the correct index.php for shared hosting
cp public_html_index.php upload_package/public_html/index.php

# Copy production environment file
cp .env.production upload_package/pos_system/.env

# Create storage directories with proper structure
echo "📂 Creating storage directories..."
mkdir -p upload_package/pos_system/storage/app/public
mkdir -p upload_package/pos_system/storage/framework/cache
mkdir -p upload_package/pos_system/storage/framework/sessions
mkdir -p upload_package/pos_system/storage/framework/views
mkdir -p upload_package/pos_system/storage/logs

# Create empty files to maintain directory structure
touch upload_package/pos_system/storage/logs/.gitkeep
touch upload_package/pos_system/storage/framework/cache/.gitkeep
touch upload_package/pos_system/storage/framework/sessions/.gitkeep
touch upload_package/pos_system/storage/framework/views/.gitkeep

# Copy deployment guide
cp DEPLOYMENT_GUIDE.md upload_package/

echo "✅ Upload package prepared successfully!"
echo ""
echo "📋 Next steps:"
echo "1. Compress the 'upload_package' folder"
echo "2. Upload to your hosting provider"
echo "3. Extract files according to DEPLOYMENT_GUIDE.md"
echo "4. Follow the deployment guide for final setup"
echo ""
echo "📁 Package contents:"
echo "   - pos_system/ (main application - place outside public_html)"
echo "   - public_html/ (web files - place in public_html)"
echo "   - DEPLOYMENT_GUIDE.md (follow this guide)"