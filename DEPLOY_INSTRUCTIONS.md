# 🚀 วิธีการ Deploy POS System ไปยัง Shared Hosting

## ✅ ไฟล์ที่เตรียมพร้อมแล้ว

ในโฟลเดอร์ `upload_package` มีไฟล์ที่พร้อม deploy:

```
upload_package/
├── pos_system/          (Laravel application - วางนอก public_html)
├── public_html/         (ไฟล์เว็บ - วางใน public_html)
└── DEPLOYMENT_GUIDE.md  (คู่มือรายละเอียด)
```

## 📋 ขั้นตอนการ Deploy (ง่าย ๆ 5 ขั้นตอน)

### ขั้นตอนที่ 1: อัปโหลดไฟล์
1. **Zip โฟลเดอร์ upload_package** ทั้งหมด
2. **อัปโหลด** ไปยัง hosting ของคุณ
3. **แตกไฟล์** ใน File Manager

### ขั้นตอนที่ 2: จัดเรียงไฟล์
```
domains/zp12825.tld/
├── pos_system/          ← ย้ายจาก upload_package/pos_system/
└── public_html/         ← ย้ายเนื้อหาจาก upload_package/public_html/
    ├── index.php
    ├── css/
    ├── js/
    ├── build/
    └── .htaccess
```

### ขั้นตอนที่ 3: แก้ไข index.php
**เลือกไฟล์ index.php ที่ถูกต้อง:**

สำหรับโครงสร้าง hosting ของคุณ ใช้:
```php
// แทนที่เนื้อหาใน public_html/index.php ด้วย:
<?php
require __DIR__.'/../pos_system/vendor/autoload.php';
$app = require_once __DIR__.'/../pos_system/bootstrap/app.php';
// ... (ใช้เนื้อหาจาก public_html_index_2levels.php)
```

### ขั้นตอนที่ 4: สร้าง Database
1. เข้า **cPanel → MySQL Databases**
2. สร้าง Database ใหม่
3. สร้าง User และกำหนดสิทธิ์
4. บันทึก: Database Name, Username, Password

### ขั้นตอนที่ 5: แก้ไข .env
แก้ไขไฟล์ `pos_system/.env`:
```env
APP_URL=https://yourdomain.com
DB_HOST=localhost
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

## 🔧 รัน Migration (ผ่าน Terminal/SSH)
```bash
cd pos_system
php artisan migrate --force
php artisan key:generate --force
php artisan storage:link
```

## ⚠️ ปัญหาที่พบบ่อย

### ❌ 500 Error
- ตรวจสอบ path ใน index.php
- ตรวจสอบ file permissions (755 สำหรับ folders, 644 สำหรับ files)

### ❌ Database Error
- ตรวจสอบข้อมูล database ใน .env
- ทดสอบการเชื่อมต่อ

### ❌ CSS/JS ไม่โหลด
- ตรวจสอบว่าคัดลอกไฟล์จาก public_html ครบถ้วน
- ตรวจสอบ APP_URL ใน .env

## 📞 ทดสอบ
1. เข้า yourdomain.com
2. ควรเห็นหน้า login
3. ทดสอบ login ด้วย admin account

---

**💡 หมายเหตุ:** หากยังมีปัญหา ให้ดู error log ใน cPanel หรือติดต่อ support ของ hosting