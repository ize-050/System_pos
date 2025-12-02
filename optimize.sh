#!/bin/bash

# Optimize Script for Laravel POS System
# ใช้สำหรับ optimize และ cache ระบบ

echo "⚡ กำลัง Optimize ระบบ..."
echo ""

# Clear all cache first
echo "🧹 Clear Cache ก่อน..."
php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan clear-compiled

echo ""
echo "📦 Cache Config..."
php artisan config:cache

echo "🛣️  Cache Routes..."
php artisan route:cache

echo "👁️  Cache Views..."
php artisan view:cache

echo "⚡ Optimize..."
php artisan optimize

echo ""
echo "✅ Optimize เรียบร้อยแล้ว!"
echo ""
echo "📝 หมายเหตุ: ใช้สำหรับ Production เท่านั้น"
echo "   สำหรับ Development ให้ใช้ clear-cache.sh แทน"
