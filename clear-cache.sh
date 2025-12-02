#!/bin/bash

# Clear Cache Script for Laravel POS System
# ใช้สำหรับล้าง cache ทั้งหมด

echo "🧹 กำลังล้าง Cache..."
echo ""

# Clear application cache
echo "📦 Clear Application Cache..."
php artisan cache:clear

# Clear route cache
echo "🛣️  Clear Route Cache..."
php artisan route:clear

# Clear config cache
echo "⚙️  Clear Config Cache..."
php artisan config:clear

# Clear view cache
echo "👁️  Clear View Cache..."
php artisan view:clear

# Clear compiled classes
echo "🔧 Clear Compiled..."
php artisan clear-compiled

# Optimize (optional - comment out if not needed)
# echo "⚡ Optimizing..."
# php artisan optimize

echo ""
echo "✅ ล้าง Cache เรียบร้อยแล้ว!"
echo ""
echo "📝 หมายเหตุ: ถ้าต้องการ optimize ให้เอา comment ออกจาก php artisan optimize"
