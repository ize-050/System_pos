# 🔐 01. ระบบ Authentication & Authorization

## ✅ Checklist

### Login/Logout

- [ ] เข้าสู่ระบบด้วย Email + Password
- [ ] ออกจากระบบ
- [ ] Remember Me
- [ ] Redirect หลัง Login ไปหน้า Dashboard

### User Roles

- [ ] Admin - เข้าถึงได้ทุกหน้า
- [ ] Manager - จัดการสินค้า, ลูกค้า, รายงาน
- [ ] Cashier - POS, ขายสินค้า
- [ ] Accountant - รายงาน, ใบสั่งซื้อ

### Permission Control

- [ ] หน้าที่ไม่มีสิทธิ์ → Redirect หรือแสดง 403
- [ ] เมนูแสดงตาม Role

### Profile

- [ ] แก้ไขชื่อ
- [ ] แก้ไข Email
- [ ] เปลี่ยนรหัสผ่าน

---

## 📁 ไฟล์ที่เกี่ยวข้อง

### Controllers

- `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- `app/Http/Controllers/ProfileController.php`

### Middleware

- `app/Http/Middleware/RoleMiddleware.php`

### Views

- `resources/js/Pages/Auth/Login.vue`
- `resources/js/Pages/Profile/Edit.vue`

### Routes

- `routes/auth.php`

---

## 🧪 วิธีทดสอบ

1. ไปที่ `/login`
2. Login ด้วย Admin account
3. ตรวจสอบว่าเห็นเมนูทั้งหมด
4. Logout แล้ว Login ด้วย Cashier
5. ตรวจสอบว่าเห็นเฉพาะเมนู POS

---

## 📝 หมายเหตุ

- Default Admin: `admin@example.com` / `password`
