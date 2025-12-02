@echo off
REM Clear Cache Script for Laravel POS System (Windows)
REM ใช้สำหรับล้าง cache ทั้งหมด

echo 🧹 กำลังล้าง Cache...
echo.

REM Clear application cache
echo 📦 Clear Application Cache...
php artisan cache:clear

REM Clear route cache
echo 🛣️  Clear Route Cache...
php artisan route:clear

REM Clear config cache
echo ⚙️  Clear Config Cache...
php artisan config:clear

REM Clear view cache
echo 👁️  Clear View Cache...
php artisan view:clear

REM Clear compiled classes
echo 🔧 Clear Compiled...
php artisan clear-compiled

echo.
echo ✅ ล้าง Cache เรียบร้อยแล้ว!
echo.
pause
