# 🚀 คู่มือการ Deploy POS System ไปยัง Shared Hosting

## 📋 ขั้นตอนการเตรียมไฟล์

### 1. เตรียม Production Files
```bash
# 1. Build frontend assets
npm run build

# 2. Optimize Laravel (รัน optimize-production.sh)
chmod +x optimize-production.sh
./optimize-production.sh
```

### 2. โครงสร้างไฟล์บน Shared Hosting

```
public_html/                    (Document Root ของ hosting)
├── index.php                   (ใช้ไฟล์ public_html_index.php)
├── .htaccess                   (คัดลอกจาก pos_system/public/.htaccess)
├── css/                        (คัดลอกจาก pos_system/public/css/)
├── js/                         (คัดลอกจาก pos_system/public/js/)
├── build/                      (คัดลอกจาก pos_system/public/build/)
├── favicon.ico
└── robots.txt

pos_system/                     (อยู่นอก public_html เพื่อความปลอดภัย)
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env                        (ใช้ .env.production แล้วแก้ไข)
├── artisan
└── composer.json
```

## 🔧 ขั้นตอนการ Deploy

### ขั้นตอนที่ 1: Upload ไฟล์
1. **Upload โฟลเดอร์ pos_system** ไปยังระดับเดียวกับ public_html (ไม่ใส่ใน public_html)
2. **Copy ไฟล์จาก pos_system/public/** ไปยัง public_html/
3. **แทนที่ public_html/index.php** ด้วยไฟล์ `public_html_index.php`

### ขั้นตอนที่ 2: ตั้งค่า Database
1. สร้าง MySQL Database ใน cPanel
2. สร้าง Database User และกำหนดสิทธิ์
3. บันทึก: Database Name, Username, Password, Host

### ขั้นตอนที่ 3: แก้ไข .env
```bash
# คัดลอก .env.production เป็น .env แล้วแก้ไข:

APP_NAME="POS System"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost                    # หรือ IP ที่ hosting ให้
DB_PORT=3306
DB_DATABASE=your_database_name       # ชื่อ database ที่สร้าง
DB_USERNAME=your_database_username   # username ที่สร้าง
DB_PASSWORD=your_database_password   # password ที่ตั้ง

SANCTUM_STATEFUL_DOMAINS=yourdomain.com
SESSION_DOMAIN=yourdomain.com
```

### ขั้นตอนที่ 4: รัน Migration (ผ่าน Terminal หรือ SSH)
```bash
cd pos_system
php artisan migrate --force
php artisan db:seed --force  # ถ้ามี seeder
```

### ขั้นตอนที่ 5: ตั้งค่า File Permissions
```bash
# ตั้งค่าสิทธิ์ไฟล์
find pos_system -type f -exec chmod 644 {} \;
find pos_system -type d -exec chmod 755 {} \;

# ตั้งค่าสิทธิ์พิเศษสำหรับ storage และ bootstrap/cache
chmod -R 775 pos_system/storage
chmod -R 775 pos_system/bootstrap/cache
```

## ⚠️ สิ่งสำคัญที่ต้องตรวจสอบ

### 1. Path ใน index.php
ตรวจสอบว่า path ใน `public_html/index.php` ถูกต้อง:
```php
// ถ้า pos_system อยู่ระดับเดียวกับ public_html
require __DIR__.'/../pos_system/vendor/autoload.php';
$app = require_once __DIR__.'/../pos_system/bootstrap/app.php';

// ถ้า pos_system อยู่ใน subfolder
require __DIR__.'/../../new_pos/pos_system/vendor/autoload.php';
$app = require_once __DIR__.'/../../new_pos/pos_system/bootstrap/app.php';
```

### 2. .htaccess Configuration
ตรวจสอบว่า `.htaccess` ใน public_html มีเนื้อหาจาก `pos_system/public/.htaccess`

### 3. Storage Link
หลังจาก deploy แล้ว รัน:
```bash
php artisan storage:link
```

## 🔍 การแก้ไขปัญหาที่พบบ่อย

### ปัญหา: 500 Internal Server Error
- ตรวจสอบ path ใน index.php
- ตรวจสอบ file permissions
- ดู error log ใน cPanel

### ปัญหา: Database Connection Error
- ตรวจสอบข้อมูล database ใน .env
- ทดสอบการเชื่อมต่อ database

### ปัญหา: CSS/JS ไม่โหลด
- ตรวจสอบว่าคัดลอกไฟล์จาก public/ ครบถ้วน
- ตรวจสอบ APP_URL ใน .env

### ปัญหา: Session/Authentication
- ตรวจสอบ SESSION_DOMAIN ใน .env
- ตรวจสอบ SANCTUM_STATEFUL_DOMAINS

## 📞 การทดสอบหลัง Deploy

1. เข้าไปที่ yourdomain.com
2. ทดสอบการ login
3. ทดสอบฟีเจอร์หลักของ POS
4. ตรวจสอบ responsive design บนมือถือ

## 🔄 การอัปเดตในอนาคต

เมื่อต้องการอัปเดตระบบ:
1. Build assets ใหม่: `npm run build`
2. Upload ไฟล์ที่เปลี่ยนแปลง
3. รัน migration ใหม่ (ถ้ามี): `php artisan migrate --force`
4. Clear cache: `php artisan cache:clear`