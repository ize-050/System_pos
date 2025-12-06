# 🚀 คู่มือ Deploy Laravel POS ไป Shared Hosting

## 📋 ข้อกำหนดของ Hosting

- **PHP**: 7.4 หรือสูงกว่า
- **MySQL**: 5.7 หรือสูงกว่า
- **Extensions ที่ต้องมี**: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD

---

## 🔧 ขั้นตอนเตรียมไฟล์ (บนเครื่อง Local)

### 1. Build Frontend Assets
```bash
npm run build
```

### 2. Optimize Laravel
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 3. สร้าง APP_KEY ใหม่ (ถ้ายังไม่มี)
```bash
php artisan key:generate --show
```
**เก็บ key นี้ไว้ใส่ใน .env บน server**

---

## 📁 โครงสร้างไฟล์บน Shared Hosting

```
/home/username/
├── public_html/          ← เนื้อหาจากโฟลเดอร์ public/
│   ├── index.php         ← แก้ไข path
│   ├── .htaccess
│   ├── build/
│   ├── storage/          ← symlink ไปยัง ../pos_system/storage/app/public
│   └── ...
│
└── pos_system/           ← โฟลเดอร์หลักของ Laravel (นอก public_html)
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/
    ├── .env              ← ไฟล์ config สำหรับ production
    └── ...
```

---

## 📤 ขั้นตอน Upload

### 1. Upload โฟลเดอร์หลัก
Upload ทุกอย่าง **ยกเว้น** โฟลเดอร์ `public/` ไปที่ `/home/username/pos_system/`

**ไฟล์/โฟลเดอร์ที่ไม่ต้อง upload:**
- `node_modules/`
- `.git/`
- `tests/`
- `.env` (สร้างใหม่บน server)

### 2. Upload โฟลเดอร์ public
Upload เนื้อหาใน `public/` ไปที่ `/home/username/public_html/`

### 3. แก้ไข index.php
แก้ไขไฟล์ `/public_html/index.php`:

```php
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// ===== แก้ไข path ตรงนี้ =====
require __DIR__.'/../pos_system/vendor/autoload.php';
$app = require_once __DIR__.'/../pos_system/bootstrap/app.php';
// =============================

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
```

---

## ⚙️ ตั้งค่าบน Server

### 1. สร้างไฟล์ .env
สร้างไฟล์ `/home/username/pos_system/.env` และใส่ข้อมูล:

```env
APP_NAME="POS System"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### 2. ตั้งค่า Permission
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 644 storage/logs/
```

### 3. สร้าง Storage Link
รันผ่าน SSH หรือ Terminal ใน cPanel:
```bash
cd /home/username/pos_system
php artisan storage:link
```

**หรือสร้าง symlink manual:**
```bash
ln -s /home/username/pos_system/storage/app/public /home/username/public_html/storage
```

### 4. Import Database
- สร้าง database ใหม่ใน cPanel
- Export database จาก local: `mysqldump -u root pos_system > pos_system.sql`
- Import ผ่าน phpMyAdmin

---

## 🔒 ไฟล์ .htaccess สำหรับ public_html

สร้างหรือแก้ไข `/public_html/.htaccess`:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# PHP Settings
<IfModule mod_php7.c>
    php_value upload_max_filesize 10M
    php_value post_max_size 12M
    php_value max_execution_time 300
    php_value memory_limit 256M
</IfModule>
```

---

## 🧪 ทดสอบหลัง Deploy

1. เข้าเว็บไซต์ `https://yourdomain.com`
2. ทดสอบ login
3. ทดสอบ POS ขายสินค้า
4. ทดสอบ upload รูปภาพ
5. ทดสอบพิมพ์ใบเสร็จ

---

## ❗ แก้ปัญหาที่พบบ่อย

### 1. หน้าขาว / 500 Error
- ตรวจสอบ `storage/logs/laravel.log`
- ตรวจสอบ permission ของ `storage/` และ `bootstrap/cache/`

### 2. CSS/JS ไม่โหลด
- ตรวจสอบว่า `APP_URL` ใน `.env` ถูกต้อง
- ตรวจสอบว่า `public/build/` มีไฟล์ครบ

### 3. รูปภาพไม่แสดง
- ตรวจสอบ symlink `storage`
- รัน `php artisan storage:link`

### 4. Session หมดอายุเร็ว
- เพิ่ม `SESSION_LIFETIME=1440` ใน `.env`

---

## 📞 ข้อมูลที่ต้องขอจาก Hosting

1. **Database**:
   - Host (ปกติคือ `localhost`)
   - Database name
   - Username
   - Password

2. **PHP Version**: ต้อง 7.4+

3. **SSH Access**: สำหรับรัน artisan commands (ถ้ามี)

---

## 🔄 การ Update ในอนาคต

1. Build assets ใหม่: `npm run build`
2. Upload ไฟล์ที่เปลี่ยนแปลง
3. Clear cache บน server:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

**สร้างเมื่อ:** $(date)
**Laravel Version:** 9.x
**PHP Version:** 7.4+
