# 📋 PRD: ระบบจัดการลูกหนี้-เจ้าหนี้ (Accounts Receivable & Payable Management)

## 📌 Document Information
- **Project:** POS System - Accounts Management Module
- **Version:** 1.0
- **Date:** 2025-10-04
- **Author:** Product Owner
- **Status:** Draft

---

## 🎯 Executive Summary

เพิ่มระบบจัดการลูกหนี้-เจ้าหนี้ในระบบ POS เพื่อติดตามและจัดการบัญชีทางการเงินแบบครบวงจร ประกอบด้วย:
1. **ระบบลูกหนี้ (Accounts Receivable)** - ติดตามลูกค้าที่ซื้อสินค้าเชื่อจากเรา
2. **ระบบเจ้าหนี้ (Accounts Payable)** - ติดตามการซื้อสินค้าเชื่อจากซัพพลายเออร์

---

## 🔍 Problem Statement

### ปัญหาปัจจุบัน
1. ระบบมีเฉพาะการติดตามลูกหนี้ (Debtors) แต่ไม่มีระบบเจ้าหนี้
2. ไม่มีหน้าสรุปภาพรวมของลูกหนี้-เจ้าหนี้
3. ไม่สามารถติดตามการซื้อสินค้าเชื่อจากซัพพลายเออร์
4. ไม่มีระบบบันทึกการชำระเงินแบบละเอียด

### ผลกระทบ
- ไม่สามารถจัดการกระแสเงินสดได้อย่างมีประสิทธิภาพ
- ไม่ทราบภาพรวมหนี้สินของธุรกิจ
- ยากต่อการติดตามและเรียกเก็บเงิน

---

## 👥 Target Users

### Primary Users
- **เจ้าของร้าน/ผู้จัดการ (Admin/Manager)** - ดูภาพรวม จัดการลูกหนี้-เจ้าหนี้
- **พนักงานบัญชี (Accountant)** - บันทึกการชำระเงิน ติดตามหนี้

### Secondary Users
- **แคชเชียร์ (Cashier)** - บันทึกการขายเชื่อ

---

## 🎯 Goals & Success Metrics

### Business Goals
1. ติดตามลูกหนี้และเจ้าหนี้ได้อย่างครบถ้วน
2. ลดเวลาในการจัดการบัญชีลูกหนี้-เจ้าหนี้ 50%
3. ลดหนี้สูญจากการติดตามไม่ทัน

### Success Metrics
- จำนวนลูกหนี้ที่ชำระเงินตรงเวลาเพิ่มขึ้น 30%
- ระยะเวลาเฉลี่ยในการเรียกเก็บเงินลดลง 40%
- ความพึงพอใจของผู้ใช้งาน >= 4.5/5

---

## 📊 Feature Requirements

## 1. ระบบลูกหนี้ (Accounts Receivable - AR)

### 1.1 หน้าสรุปลูกหนี้ (AR Summary Dashboard)

#### Layout
```
┌─────────────────────────────────────────────────────────────┐
│  📊 สรุปลูกหนี้ - ลูกค้าที่ซื้อสินค้าเชื่อ                    │
├─────────────────────────────────────────────────────────────┤
│  💰 ยอดลูกหนี้ทั้งหมด: 150,000 บาท                          │
│  👥 จำนวนลูกหนี้: 25 ราย                                     │
│  ⚠️  เกินกำหนดชำระ: 5 ราย (20,000 บาท)                      │
│  📅 ครบกำหนดใน 7 วัน: 8 ราย (45,000 บาท)                    │
├─────────────────────────────────────────────────────────────┤
│  [ค้นหา...] [กรองตามสถานะ▼] [ส่งออก Excel] [+ เพิ่มรายการ]  │
├─────────────────────────────────────────────────────────────┤
│  ตารางรายการลูกหนี้                                          │
└─────────────────────────────────────────────────────────────┘
```

#### ตารางรายการลูกหนี้ (AR Table)

| คอลัมน์ | ประเภท | คำอธิบาย | Required |
|---------|--------|----------|----------|
| วันที่ | Date | วันที่ขายเชื่อ | ✅ |
| รายการซื้อ | Text | เลขที่บิล/รายการสินค้า | ✅ |
| ชื่อลูกค้า | Text | ชื่อลูกค้า (Link to Customer) | ✅ |
| จำนวนเงิน | Decimal | ยอดเงินทั้งหมด | ✅ |
| เท่าไหร่ | Decimal | ยอดที่ชำระแล้ว | ✅ |
| จ่ายเงิน | Decimal | ยอดคงเหลือที่ต้องชำระ | ✅ |
| วันครบกำหนด | Date | วันที่ต้องชำระเงิน | ✅ |
| สถานะ | Badge | ปกติ/เกินกำหนด/ชำระแล้ว | ✅ |
| ธนาคาร | Text | ธนาคารที่โอนเงิน (ถ้ามี) | ❌ |
| หมายเหตุ | Text | บันทึกเพิ่มเติม | ❌ |
| Actions | Buttons | ดู/แก้ไข/รับชำระ/พิมพ์ | ✅ |

#### สถานะลูกหนี้ (AR Status)
- 🟢 **ปกติ (Normal)** - ยังไม่ครบกำหนดชำระ
- 🟡 **ใกล้ครบกำหนด (Due Soon)** - เหลือเวลา <= 7 วัน
- 🔴 **เกินกำหนด (Overdue)** - เกินวันครบกำหนดแล้ว
- ✅ **ชำระแล้ว (Paid)** - ชำระครบแล้ว
- ⚠️ **ชำระบางส่วน (Partial)** - ชำระบางส่วน

### 1.2 ฟอร์มรับชำระเงินลูกหนี้ (AR Payment Form)

#### Fields
```
┌─────────────────────────────────────────┐
│  รับชำระเงินลูกหนี้                      │
├─────────────────────────────────────────┤
│  ลูกค้า: [ชื่อลูกค้า]                   │
│  เลขที่บิล: [SALE-2025-0001]           │
│  ยอดเงินทั้งหมด: 10,000 บาท            │
│  ชำระแล้ว: 3,000 บาท                   │
│  คงเหลือ: 7,000 บาท                    │
├─────────────────────────────────────────┤
│  วันที่รับชำระ: [2025-10-04] *         │
│  จำนวนเงินที่รับ: [_______] บาท *      │
│  วิธีการชำระ: [เงินสด▼] *              │
│    - เงินสด                             │
│    - โอนเงิน                            │
│    - เช็ค                               │
│  ธนาคาร: [_______] (ถ้าโอนเงิน)        │
│  เลขที่อ้างอิง: [_______]              │
│  หมายเหตุ: [_______]                   │
│  แนบหลักฐาน: [📎 อัปโหลด]             │
├─────────────────────────────────────────┤
│  [ยกเลิก]  [บันทึกการชำระเงิน]         │
└─────────────────────────────────────────┘
```

### 1.3 ประวัติการชำระเงิน (AR Payment History)
- แสดงประวัติการชำระเงินทั้งหมดของลูกหนี้แต่ละราย
- Timeline แสดงการชำระเงินแต่ละครั้ง
- สามารถพิมพ์ใบเสร็จรับเงินได้

---

## 2. ระบบเจ้าหนี้ (Accounts Payable - AP)

### 2.1 หน้าสรุปเจ้าหนี้ (AP Summary Dashboard)

#### Layout
```
┌─────────────────────────────────────────────────────────────┐
│  📊 สรุปเจ้าหนี้ - การซื้อสินค้าเชื่อจากซัพพลายเออร์          │
├─────────────────────────────────────────────────────────────┤
│  💰 ยอดเจ้าหนี้ทั้งหมด: 250,000 บาท                         │
│  🏢 จำนวนซัพพลายเออร์: 12 ราย                               │
│  ⚠️  เกินกำหนดชำระ: 3 ราย (30,000 บาท)                     │
│  📅 ครบกำหนดใน 7 วัน: 5 ราย (80,000 บาท)                   │
├─────────────────────────────────────────────────────────────┤
│  [ค้นหา...] [กรองตามสถานะ▼] [ส่งออก Excel] [+ เพิ่มรายการ]  │
├─────────────────────────────────────────────────────────────┤
│  ตารางรายการเจ้าหนี้                                         │
└─────────────────────────────────────────────────────────────┘
```

#### ตารางรายการเจ้าหนี้ (AP Table)

| คอลัมน์ | ประเภท | คำอธิบาย | Required |
|---------|--------|----------|----------|
| วันที่ | Date | วันที่ซื้อเชื่อ | ✅ |
| ผู้จัดจำหน่าย | Text | ชื่อซัพพลายเออร์ (Link to Supplier) | ✅ |
| รายการสินค้า | Text | เลขที่ PO/รายการสินค้า | ✅ |
| จำนวนเงินที่ต้องจ่าย | Decimal | ยอดเงินทั้งหมด | ✅ |
| ค่าเกินที่ต้องจ่าย | Decimal | ค่าปรับ/ดอกเบี้ย (ถ้ามี) | ❌ |
| ค่าเกินที่จ่ายแล้ว | Decimal | ยอดที่ชำระแล้ว | ✅ |
| จำนวนเงินคงเหลือ | Decimal | ยอดคงเหลือที่ต้องชำระ | ✅ |
| วันครบกำหนด | Date | วันที่ต้องชำระเงิน | ✅ |
| สถานะ | Badge | ปกติ/เกินกำหนด/ชำระแล้ว | ✅ |
| ช่องทาง | Text | วิธีการชำระเงิน | ❌ |
| หมายเหตุ | Text | บันทึกเพิ่มเติม | ❌ |
| Actions | Buttons | ดู/แก้ไข/ชำระ/พิมพ์ | ✅ |

#### สถานะเจ้าหนี้ (AP Status)
- 🟢 **ปกติ (Normal)** - ยังไม่ครบกำหนดชำระ
- 🟡 **ใกล้ครบกำหนด (Due Soon)** - เหลือเวลา <= 7 วัน
- 🔴 **เกินกำหนด (Overdue)** - เกินวันครบกำหนดแล้ว
- ✅ **ชำระแล้ว (Paid)** - ชำระครบแล้ว
- ⚠️ **ชำระบางส่วน (Partial)** - ชำระบางส่วน

### 2.2 ฟอร์มบันทึกการซื้อเชื่อ (AP Purchase Form)

#### Fields
```
┌─────────────────────────────────────────┐
│  บันทึกการซื้อสินค้าเชื่อ                │
├─────────────────────────────────────────┤
│  ผู้จัดจำหน่าย: [เลือกซัพพลายเออร์▼] * │
│  วันที่ซื้อ: [2025-10-04] *            │
│  เลขที่ใบสั่งซื้อ: [PO-2025-0001] *   │
│  วันครบกำหนดชำระ: [2025-11-04] *      │
├─────────────────────────────────────────┤
│  รายการสินค้า:                          │
│  ┌─────────────────────────────────┐   │
│  │ สินค้า | จำนวน | ราคา | รวม    │   │
│  │ [เลือก] [___] [___] [____]     │   │
│  │ [+ เพิ่มรายการ]                 │   │
│  └─────────────────────────────────┘   │
├─────────────────────────────────────────┤
│  ยอดรวม: [_______] บาท *               │
│  ส่วนลด: [_______] บาท                 │
│  ยอดสุทธิ: [_______] บาท               │
│  เงื่อนไขการชำระ: [_______]            │
│  หมายเหตุ: [_______]                   │
│  แนบเอกสาร: [📎 อัปโหลด]              │
├─────────────────────────────────────────┤
│  [ยกเลิก]  [บันทึก]                    │
└─────────────────────────────────────────┘
```

### 2.3 ฟอร์มชำระเงินเจ้าหนี้ (AP Payment Form)

#### Fields
```
┌─────────────────────────────────────────┐
│  ชำระเงินเจ้าหนี้                        │
├─────────────────────────────────────────┤
│  ผู้จัดจำหน่าย: [ชื่อซัพพลายเออร์]     │
│  เลขที่ PO: [PO-2025-0001]             │
│  ยอดเงินทั้งหมด: 50,000 บาท            │
│  ชำระแล้ว: 20,000 บาท                  │
│  คงเหลือ: 30,000 บาท                   │
├─────────────────────────────────────────┤
│  วันที่ชำระ: [2025-10-04] *            │
│  จำนวนเงินที่จ่าย: [_______] บาท *     │
│  วิธีการชำระ: [โอนเงิน▼] *             │
│    - เงินสด                             │
│    - โอนเงิน                            │
│    - เช็ค                               │
│  ธนาคาร: [_______] *                   │
│  เลขที่อ้างอิง: [_______]              │
│  หมายเหตุ: [_______]                   │
│  แนบหลักฐาน: [📎 อัปโหลด]             │
├─────────────────────────────────────────┤
│  [ยกเลิก]  [บันทึกการชำระเงิน]         │
└─────────────────────────────────────────┘
```

---

## 3. หน้าสรุปภาพรวม (Overview Dashboard)

### 3.1 Dashboard Layout

```
┌─────────────────────────────────────────────────────────────┐
│  💼 สรุปภาพรวมบัญชีลูกหนี้-เจ้าหนี้                          │
├─────────────────────────────────────────────────────────────┤
│  ┌──────────────────┐  ┌──────────────────┐                │
│  │ 📊 ลูกหนี้        │  │ 📊 เจ้าหนี้       │                │
│  │ 150,000 บาท      │  │ 250,000 บาท      │                │
│  │ 25 ราย           │  │ 12 ราย           │                │
│  └──────────────────┘  └──────────────────┘                │
│  ┌──────────────────┐  ┌──────────────────┐                │
│  │ ⚠️ เกินกำหนด     │  │ 💰 กระแสเงินสด    │                │
│  │ ลูกหนี้: 20,000  │  │ สุทธิ: -100,000  │                │
│  │ เจ้าหนี้: 30,000 │  │ (เจ้าหนี้ > ลูกหนี้)│              │
│  └──────────────────┘  └──────────────────┘                │
├─────────────────────────────────────────────────────────────┤
│  📈 กราฟแสดงแนวโน้มลูกหนี้-เจ้าหนี้ (6 เดือน)               │
│  [กราฟเส้น]                                                 │
├─────────────────────────────────────────────────────────────┤
│  ⏰ รายการที่ต้องดำเนินการ                                  │
│  - ลูกหนี้เกินกำหนด 5 ราย                                   │
│  - เจ้าหนี้ครบกำหนดใน 7 วัน 5 ราย                          │
│  - ลูกหนี้ครบกำหนดใน 7 วัน 8 ราย                           │
└─────────────────────────────────────────────────────────────┘
```

---

## 🗄️ Database Schema

### 1. ตาราง `account_receivables` (ลูกหนี้)

```sql
CREATE TABLE account_receivables (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ar_number VARCHAR(50) UNIQUE NOT NULL COMMENT 'เลขที่ลูกหนี้ AR-YYYY-XXXX',
    sale_id BIGINT UNSIGNED NULL COMMENT 'FK to sales',
    customer_id BIGINT UNSIGNED NOT NULL COMMENT 'FK to customers',
    invoice_number VARCHAR(50) NULL COMMENT 'เลขที่บิล',
    invoice_date DATE NOT NULL COMMENT 'วันที่ขาย',
    due_date DATE NOT NULL COMMENT 'วันครบกำหนดชำระ',
    total_amount DECIMAL(15,2) NOT NULL COMMENT 'ยอดเงินทั้งหมด',
    paid_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'ยอดที่ชำระแล้ว',
    remaining_amount DECIMAL(15,2) NOT NULL COMMENT 'ยอดคงเหลือ',
    status ENUM('normal', 'due_soon', 'overdue', 'partial', 'paid') DEFAULT 'normal',
    payment_terms VARCHAR(255) NULL COMMENT 'เงื่อนไขการชำระเงิน',
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE SET NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_customer (customer_id),
    INDEX idx_status (status),
    INDEX idx_due_date (due_date),
    INDEX idx_invoice_date (invoice_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2. ตาราง `ar_payments` (การชำระเงินลูกหนี้)

```sql
CREATE TABLE ar_payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_number VARCHAR(50) UNIQUE NOT NULL COMMENT 'เลขที่ใบเสร็จ ARP-YYYY-XXXX',
    ar_id BIGINT UNSIGNED NOT NULL COMMENT 'FK to account_receivables',
    payment_date DATE NOT NULL COMMENT 'วันที่รับชำระ',
    amount DECIMAL(15,2) NOT NULL COMMENT 'จำนวนเงินที่รับ',
    payment_method ENUM('cash', 'transfer', 'cheque') NOT NULL,
    bank_name VARCHAR(100) NULL COMMENT 'ธนาคาร',
    reference_number VARCHAR(100) NULL COMMENT 'เลขที่อ้างอิง',
    receipt_image VARCHAR(255) NULL COMMENT 'รูปหลักฐานการชำระเงิน',
    notes TEXT NULL,
    received_by BIGINT UNSIGNED NULL COMMENT 'ผู้รับเงิน',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (ar_id) REFERENCES account_receivables(id) ON DELETE CASCADE,
    FOREIGN KEY (received_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_ar (ar_id),
    INDEX idx_payment_date (payment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3. ตาราง `account_payables` (เจ้าหนี้)

```sql
CREATE TABLE account_payables (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ap_number VARCHAR(50) UNIQUE NOT NULL COMMENT 'เลขที่เจ้าหนี้ AP-YYYY-XXXX',
    supplier_id BIGINT UNSIGNED NOT NULL COMMENT 'FK to suppliers',
    po_number VARCHAR(50) NULL COMMENT 'เลขที่ใบสั่งซื้อ',
    purchase_date DATE NOT NULL COMMENT 'วันที่ซื้อ',
    due_date DATE NOT NULL COMMENT 'วันครบกำหนดชำระ',
    total_amount DECIMAL(15,2) NOT NULL COMMENT 'ยอดเงินทั้งหมด',
    discount_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'ส่วนลด',
    net_amount DECIMAL(15,2) NOT NULL COMMENT 'ยอดสุทธิ',
    paid_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'ยอดที่ชำระแล้ว',
    remaining_amount DECIMAL(15,2) NOT NULL COMMENT 'ยอดคงเหลือ',
    penalty_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'ค่าปรับ/ดอกเบี้ย',
    status ENUM('normal', 'due_soon', 'overdue', 'partial', 'paid') DEFAULT 'normal',
    payment_terms VARCHAR(255) NULL COMMENT 'เงื่อนไขการชำระเงิน',
    document_path VARCHAR(255) NULL COMMENT 'เอกสารแนบ',
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_supplier (supplier_id),
    INDEX idx_status (status),
    INDEX idx_due_date (due_date),
    INDEX idx_purchase_date (purchase_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4. ตาราง `ap_items` (รายการสินค้าในการซื้อเชื่อ)

```sql
CREATE TABLE ap_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ap_id BIGINT UNSIGNED NOT NULL COMMENT 'FK to account_payables',
    product_id BIGINT UNSIGNED NULL COMMENT 'FK to products',
    product_name VARCHAR(255) NOT NULL COMMENT 'ชื่อสินค้า',
    quantity DECIMAL(10,2) NOT NULL COMMENT 'จำนวน',
    unit_price DECIMAL(15,2) NOT NULL COMMENT 'ราคาต่อหน่วย',
    total_price DECIMAL(15,2) NOT NULL COMMENT 'ราคารวม',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (ap_id) REFERENCES account_payables(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    
    INDEX idx_ap (ap_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 5. ตาราง `ap_payments` (การชำระเงินเจ้าหนี้)

```sql
CREATE TABLE ap_payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_number VARCHAR(50) UNIQUE NOT NULL COMMENT 'เลขที่การชำระ APP-YYYY-XXXX',
    ap_id BIGINT UNSIGNED NOT NULL COMMENT 'FK to account_payables',
    payment_date DATE NOT NULL COMMENT 'วันที่ชำระ',
    amount DECIMAL(15,2) NOT NULL COMMENT 'จำนวนเงินที่จ่าย',
    payment_method ENUM('cash', 'transfer', 'cheque') NOT NULL,
    bank_name VARCHAR(100) NULL COMMENT 'ธนาคาร',
    reference_number VARCHAR(100) NULL COMMENT 'เลขที่อ้างอิง',
    receipt_image VARCHAR(255) NULL COMMENT 'รูปหลักฐานการชำระเงิน',
    notes TEXT NULL,
    paid_by BIGINT UNSIGNED NULL COMMENT 'ผู้จ่ายเงิน',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (ap_id) REFERENCES account_payables(id) ON DELETE CASCADE,
    FOREIGN KEY (paid_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_ap (ap_id),
    INDEX idx_payment_date (payment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 🎨 UI/UX Requirements

### Design Principles
1. **ใช้งานง่าย** - Interface ที่เข้าใจง่าย ไม่ซับซ้อน
2. **แสดงข้อมูลชัดเจน** - ใช้สี Badge แยกสถานะ
3. **Responsive** - รองรับทุกอุปกรณ์
4. **Quick Actions** - ปุ่มดำเนินการที่เข้าถึงง่าย

### Color Coding
- 🟢 **เขียว (Green)** - สถานะปกติ
- 🟡 **เหลือง (Yellow)** - ใกล้ครบกำหนด
- 🔴 **แดง (Red)** - เกินกำหนด
- 🔵 **น้ำเงิน (Blue)** - ชำระบางส่วน
- ⚫ **เทา (Gray)** - ชำระแล้ว

### Notifications
- แจ้งเตือนเมื่อใกล้ครบกำหนดชำระ (7 วันก่อน)
- แจ้งเตือนเมื่อเกินกำหนดชำระ
- แจ้งเตือนเมื่อมีการชำระเงิน

---

## 🔐 Access Control

### Role Permissions

| Feature | Admin | Manager | Accountant | Cashier |
|---------|-------|---------|------------|---------|
| ดูลูกหนี้ | ✅ | ✅ | ✅ | ✅ |
| เพิ่ม/แก้ไขลูกหนี้ | ✅ | ✅ | ✅ | ✅ |
| รับชำระเงินลูกหนี้ | ✅ | ✅ | ✅ | ✅ |
| ลบลูกหนี้ | ✅ | ✅ | ❌ | ❌ |
| ดูเจ้าหนี้ | ✅ | ✅ | ✅ | ❌ |
| เพิ่ม/แก้ไขเจ้าหนี้ | ✅ | ✅ | ✅ | ❌ |
| ชำระเงินเจ้าหนี้ | ✅ | ✅ | ✅ | ❌ |
| ลบเจ้าหนี้ | ✅ | ✅ | ❌ | ❌ |
| ดูรายงาน | ✅ | ✅ | ✅ | ❌ |
| Export ข้อมูล | ✅ | ✅ | ✅ | ❌ |

---

## 📱 API Endpoints

### Accounts Receivable (AR)

```
GET    /api/accounts-receivable              # List all AR
GET    /api/accounts-receivable/{id}         # Get AR details
POST   /api/accounts-receivable              # Create AR
PUT    /api/accounts-receivable/{id}         # Update AR
DELETE /api/accounts-receivable/{id}         # Delete AR
GET    /api/accounts-receivable/overdue      # Get overdue AR
GET    /api/accounts-receivable/due-soon     # Get due soon AR
POST   /api/accounts-receivable/{id}/payment # Record payment
GET    /api/accounts-receivable/{id}/payments # Get payment history
```

### Accounts Payable (AP)

```
GET    /api/accounts-payable                 # List all AP
GET    /api/accounts-payable/{id}            # Get AP details
POST   /api/accounts-payable                 # Create AP
PUT    /api/accounts-payable/{id}            # Update AP
DELETE /api/accounts-payable/{id}            # Delete AP
GET    /api/accounts-payable/overdue         # Get overdue AP
GET    /api/accounts-payable/due-soon        # Get due soon AP
POST   /api/accounts-payable/{id}/payment    # Record payment
GET    /api/accounts-payable/{id}/payments   # Get payment history
```

### Dashboard

```
GET    /api/accounts/dashboard               # Get overview data
GET    /api/accounts/summary                 # Get summary statistics
GET    /api/accounts/trends                  # Get trend data (6 months)
```

---

## 📊 Reports

### 1. รายงานลูกหนี้ (AR Report)
- รายงานลูกหนี้ทั้งหมด
- รายงานลูกหนี้เกินกำหนด
- รายงานการรับชำระเงินประจำวัน/เดือน
- รายงานอายุลูกหนี้ (Aging Report)

### 2. รายงานเจ้าหนี้ (AP Report)
- รายงานเจ้าหนี้ทั้งหมด
- รายงานเจ้าหนี้เกินกำหนด
- รายงานการชำระเงินประจำวัน/เดือน
- รายงานอายุเจ้าหนี้ (Aging Report)

### 3. รายงานกระแสเงินสด (Cash Flow Report)
- เปรียบเทียบลูกหนี้ vs เจ้าหนี้
- คาดการณ์กระแสเงินสด
- รายงานความสามารถในการชำระหนี้

### Export Formats
- Excel (.xlsx)
- PDF
- CSV

---

## 🔔 Notification System

### Email Notifications
- แจ้งเตือนลูกค้าก่อนครบกำหนดชำระ (3 วัน, 7 วัน)
- แจ้งเตือนเมื่อเกินกำหนดชำระ
- ยืนยันการรับชำระเงิน

### In-App Notifications
- แจ้งเตือนเจ้าหนี้ที่ต้องชำระ
- แจ้งเตือนลูกหนี้ที่เกินกำหนด
- สรุปรายวัน/รายสัปดาห์

---

## 🚀 Implementation Plan

### Phase 1: Database & Backend (Week 1-2)
- [ ] สร้าง Migration สำหรับตารางใหม่
- [ ] สร้าง Models (AccountReceivable, AccountPayable, ARPayment, APPayment, APItem)
- [ ] สร้าง Controllers
- [ ] สร้าง API Endpoints
- [ ] เขียน Unit Tests

### Phase 2: Frontend - AR Module (Week 3-4)
- [ ] สร้างหน้า AR Dashboard
- [ ] สร้างหน้า AR List
- [ ] สร้างฟอร์มรับชำระเงิน
- [ ] สร้างหน้าประวัติการชำระเงิน
- [ ] Integration Testing

### Phase 3: Frontend - AP Module (Week 5-6)
- [ ] สร้างหน้า AP Dashboard
- [ ] สร้างหน้า AP List
- [ ] สร้างฟอร์มบันทึกการซื้อเชื่อ
- [ ] สร้างฟอร์มชำระเงิน
- [ ] สร้างหน้าประวัติการชำระเงิน
- [ ] Integration Testing

### Phase 4: Dashboard & Reports (Week 7)
- [ ] สร้างหน้า Overview Dashboard
- [ ] สร้างระบบรายงาน
- [ ] สร้าง Export ฟังก์ชัน
- [ ] สร้างกราฟและ Charts

### Phase 5: Notifications & Polish (Week 8)
- [ ] ระบบแจ้งเตือน
- [ ] Email Templates
- [ ] UI/UX Polish
- [ ] Performance Optimization
- [ ] UAT Testing

### Phase 6: Deployment (Week 9)
- [ ] Documentation
- [ ] User Training
- [ ] Production Deployment
- [ ] Monitoring Setup

---

## ✅ Acceptance Criteria

### Must Have
- [ ] สามารถบันทึกและติดตามลูกหนี้ได้
- [ ] สามารถบันทึกและติดตามเจ้าหนี้ได้
- [ ] สามารถบันทึกการชำระเงินได้
- [ ] แสดงสถานะลูกหนี้-เจ้าหนี้ได้ถูกต้อง
- [ ] คำนวณยอดคงเหลือได้ถูกต้อง
- [ ] สามารถค้นหาและกรองข้อมูลได้
- [ ] สามารถ Export รายงานได้

### Should Have
- [ ] ระบบแจ้งเตือนอัตโนมัติ
- [ ] กราฟแสดงแนวโน้ม
- [ ] รายงาน Aging
- [ ] อัปโหลดหลักฐานการชำระเงิน

### Nice to Have
- [ ] ส่ง Email แจ้งเตือนลูกค้าอัตโนมัติ
- [ ] Integration กับระบบบัญชี
- [ ] Mobile App
- [ ] QR Code Payment

---

## 🧪 Testing Requirements

### Unit Tests
- Model validations
- Business logic calculations
- API endpoints

### Integration Tests
- Payment flow
- Status updates
- Report generation

### User Acceptance Tests
- End-to-end workflows
- UI/UX testing
- Performance testing

---

## 📚 Documentation Requirements

### Technical Documentation
- API Documentation
- Database Schema
- Code Documentation

### User Documentation
- User Manual (คู่มือการใช้งาน)
- Quick Start Guide
- FAQ
- Video Tutorials

---

## 🎯 Success Criteria

### Performance Metrics
- Page load time < 2 seconds
- API response time < 500ms
- Support 1000+ concurrent users

### Business Metrics
- ลดเวลาในการจัดการบัญชี 50%
- เพิ่มอัตราการเรียกเก็บเงินตรงเวลา 30%
- ลดหนี้สูญ 20%

### User Satisfaction
- User satisfaction score >= 4.5/5
- Training completion rate >= 90%
- Feature adoption rate >= 80%

---

## 🔄 Future Enhancements

### Phase 2 Features
- Integration กับระบบบัญชี (Accounting Software)
- ระบบวิเคราะห์ความเสี่ยงด้านเครดิต
- ระบบให้คะแนนเครดิตลูกค้า
- Auto-reconciliation กับ Bank Statement
- Multi-currency support

### Phase 3 Features
- AI-powered payment prediction
- Automated collection reminders
- Mobile App (iOS/Android)
- Blockchain-based payment verification

---

## 📞 Support & Maintenance

### Support Channels
- In-app Help Center
- Email Support
- Phone Support (Business hours)
- Live Chat

### Maintenance Plan
- Weekly backup
- Monthly security updates
- Quarterly feature updates
- Annual major version upgrade

---

## 📝 Appendix

### Glossary
- **AR (Accounts Receivable):** ลูกหนี้ - เงินที่ลูกค้าค้างชำระ
- **AP (Accounts Payable):** เจ้าหนี้ - เงินที่เราค้างชำระซัพพลายเออร์
- **Aging Report:** รายงานอายุหนี้ - แสดงระยะเวลาที่หนี้ค้างชำระ
- **Due Date:** วันครบกำหนดชำระ
- **Overdue:** เกินกำหนดชำระ
- **PO (Purchase Order):** ใบสั่งซื้อ

### References
- Accounting Standards
- Thai Tax Law
- POS System Documentation

---

**Document Version:** 1.0  
**Last Updated:** 2025-10-04  
**Next Review:** 2025-10-11  
**Approved By:** [Pending]
