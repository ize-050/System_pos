# 🔗 คู่มือการแทรกระบบลูกหนี้-เจ้าหนี้เข้ากับระบบ POS ปัจจุบัน

## 📋 สรุปจุดที่สามารถแทรกระบบใหม่

ระบบลูกหนี้-เจ้าหนี้สามารถแทรกเข้าไปในระบบ POS ปัจจุบันได้ **5 จุดหลัก**:

---

## 🎯 จุดที่ 1: เมนูหลัก (Main Navigation)

### ตำแหน่ง
เพิ่มเมนูใหม่ในแถบเมนูหลัก ระหว่าง **"ลูกหนี้ (Debtors)"** และ **"รายงาน (Reports)"**

### เมนูที่ต้องเพิ่ม
```
📊 Dashboard
├── 👥 ผู้ใช้งาน (Users)
├── 📦 สินค้า (Products)
├── 🏷️ หมวดหมู่ (Categories)
├── 👨‍👩‍👧‍👦 ลูกค้า (Customers)
├── 💰 ขายสินค้า (POS)
├── 📋 ประวัติการขาย (Sales)
├── 🎁 โปรโมชั่น (Promotions)
├── 💳 ลูกหนี้ (Debtors) ← มีอยู่แล้ว
│
├── 💼 บัญชีลูกหนี้-เจ้าหนี้ ← ✨ เพิ่มใหม่
│   ├── 📊 ภาพรวม (Overview)
│   ├── 📥 ลูกหนี้ (Receivables)
│   └── 📤 เจ้าหนี้ (Payables)
│
├── 📊 รายงาน (Reports)
└── 📈 Analytics
```

### ไฟล์ที่ต้องแก้ไข
- `resources/js/Layouts/AuthenticatedLayout.vue` หรือ
- `resources/js/Components/Navigation.vue`

---

## 🎯 จุดที่ 2: Dashboard (หน้าแรก)

### ตำแหน่ง
เพิ่ม Widget ใหม่ในหน้า Dashboard

### Widget ที่ต้องเพิ่ม

```
┌─────────────────────────────────────────────────────────────┐
│  📊 Dashboard                                                │
├─────────────────────────────────────────────────────────────┤
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐       │
│  │ ยอดขาย   │ │ สินค้า    │ │ ลูกค้า   │ │ สต็อกต่ำ │       │
│  │ วันนี้   │ │ ทั้งหมด   │ │ ทั้งหมด  │ │          │       │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘       │
│                                                              │
│  ┌──────────────────────┐ ┌──────────────────────┐ ← ✨ เพิ่ม │
│  │ 📥 ลูกหนี้           │ │ 📤 เจ้าหนี้          │         │
│  │ 150,000 บาท         │ │ 250,000 บาท         │         │
│  │ 25 ราย              │ │ 12 ราย              │         │
│  │ ⚠️ เกินกำหนด 5 ราย  │ │ ⚠️ เกินกำหนด 3 ราย  │         │
│  └──────────────────────┘ └──────────────────────┘         │
│                                                              │
│  📈 กราฟยอดขาย                                              │
│  📊 สินค้าขายดี                                             │
└─────────────────────────────────────────────────────────────┘
```

### ไฟล์ที่ต้องแก้ไข
- `resources/js/Pages/Dashboard.vue`
- `app/Http/Controllers/DashboardController.php`

### Code ที่ต้องเพิ่มใน DashboardController.php
```php
public function index()
{
    return Inertia::render('Dashboard', [
        // ... existing data
        
        // ✨ เพิ่มข้อมูลลูกหนี้-เจ้าหนี้
        'accountsReceivable' => [
            'total' => AccountReceivable::sum('remaining_amount'),
            'count' => AccountReceivable::where('status', '!=', 'paid')->count(),
            'overdue' => AccountReceivable::where('status', 'overdue')->count(),
        ],
        'accountsPayable' => [
            'total' => AccountPayable::sum('remaining_amount'),
            'count' => AccountPayable::where('status', '!=', 'paid')->count(),
            'overdue' => AccountPayable::where('status', 'overdue')->count(),
        ],
    ]);
}
```

---

## 🎯 จุดที่ 3: หน้าลูกหนี้เดิม (Debtors)

### ตำแหน่ง
ปรับปรุงหน้า `/debtors` ให้เชื่อมโยงกับระบบใหม่

### การปรับปรุง

**ก่อน (ระบบเดิม):**
```
/debtors → แสดงลูกค้าที่มี current_balance > 0
```

**หลัง (ระบบใหม่):**
```
/debtors → Redirect ไปที่ /accounts-receivable
หรือ
/debtors → แสดงข้อมูลจาก account_receivables แทน
```

### ไฟล์ที่ต้องแก้ไข
- `routes/web.php` (บรรทัด 95-104)

### Code ที่ต้องแก้ไข
```php
// ❌ เดิม
Route::get('/debtors', function () {
    $customers = \App\Models\Customer::where('current_balance', '>', 0)
        ->orderBy('current_balance', 'desc')
        ->get();
    return Inertia::render('Debtors/Index', [
        'customers' => $customers
    ]);
})->name('debtors.index');

// ✅ ใหม่ - Option 1: Redirect
Route::get('/debtors', function () {
    return redirect()->route('accounts-receivable.index');
})->name('debtors.index');

// ✅ ใหม่ - Option 2: ใช้ข้อมูลจาก AR
Route::get('/debtors', [AccountReceivableController::class, 'index'])
    ->name('debtors.index');
```

---

## 🎯 จุดที่ 4: หน้าขายสินค้า (POS)

### ตำแหน่ง
เพิ่มปุ่มสร้างลูกหนี้อัตโนมัติเมื่อขายเชื่อ

### การแทรก

**ในหน้า POS (`resources/js/Pages/POS.vue`):**

```vue
<!-- เมื่อเลือกวิธีการชำระเงิน -->
<select v-model="paymentMethod">
  <option value="cash">เงินสด</option>
  <option value="credit">บัตรเครดิต</option>
  <option value="customer_account">บัญชีลูกค้า (เชื่อ)</option> ← มีอยู่แล้ว
</select>

<!-- ✨ เพิ่ม: ถ้าเลือก customer_account -->
<div v-if="paymentMethod === 'customer_account'">
  <label>วันครบกำหนดชำระ</label>
  <input type="date" v-model="dueDate" />
  
  <label>เงื่อนไขการชำระ</label>
  <input type="text" v-model="paymentTerms" placeholder="เช่น: 30 วัน" />
</div>
```

**ใน POSController.php:**

```php
public function processSale(Request $request)
{
    // ... existing code
    
    // ✨ เพิ่ม: สร้าง Account Receivable ถ้าขายเชื่อ
    if ($request->payment_method === 'customer_account') {
        AccountReceivable::create([
            'ar_number' => $this->generateARNumber(),
            'sale_id' => $sale->id,
            'customer_id' => $request->customer_id,
            'invoice_number' => $sale->sale_number,
            'invoice_date' => now(),
            'due_date' => $request->due_date,
            'total_amount' => $sale->total_amount,
            'remaining_amount' => $sale->total_amount,
            'status' => 'normal',
            'payment_terms' => $request->payment_terms,
        ]);
    }
    
    // ... rest of code
}
```

### ไฟล์ที่ต้องแก้ไข
- `resources/js/Pages/POS.vue`
- `app/Http/Controllers/POSController.php`

---

## 🎯 จุดที่ 5: หน้าลูกค้า (Customers)

### ตำแหน่ง
เพิ่มแท็บแสดงประวัติลูกหนี้ในหน้ารายละเอียดลูกค้า

### การแทรก

**ในหน้า Customer Show (`resources/js/Pages/Customers/Show.vue`):**

```vue
<template>
  <div>
    <h1>{{ customer.name }}</h1>
    
    <!-- Tabs -->
    <div class="tabs">
      <button @click="activeTab = 'info'">ข้อมูลทั่วไป</button>
      <button @click="activeTab = 'sales'">ประวัติการซื้อ</button>
      <button @click="activeTab = 'receivables'">ลูกหนี้</button> ← ✨ เพิ่ม
    </div>
    
    <!-- Tab: ข้อมูลทั่วไป -->
    <div v-if="activeTab === 'info'">
      <!-- ... existing info -->
    </div>
    
    <!-- Tab: ประวัติการซื้อ -->
    <div v-if="activeTab === 'sales'">
      <!-- ... existing sales history -->
    </div>
    
    <!-- ✨ Tab: ลูกหนี้ -->
    <div v-if="activeTab === 'receivables'">
      <h2>ประวัติลูกหนี้</h2>
      <table>
        <thead>
          <tr>
            <th>วันที่</th>
            <th>เลขที่บิล</th>
            <th>ยอดเงิน</th>
            <th>ชำระแล้ว</th>
            <th>คงเหลือ</th>
            <th>สถานะ</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="ar in accountsReceivable" :key="ar.id">
            <td>{{ ar.invoice_date }}</td>
            <td>{{ ar.invoice_number }}</td>
            <td>{{ ar.total_amount }}</td>
            <td>{{ ar.paid_amount }}</td>
            <td>{{ ar.remaining_amount }}</td>
            <td>
              <span :class="getStatusClass(ar.status)">
                {{ getStatusLabel(ar.status) }}
              </span>
            </td>
            <td>
              <button @click="makePayment(ar)">รับชำระ</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
```

**ใน CustomerController.php:**

```php
public function show($id)
{
    $customer = Customer::with(['sales'])->findOrFail($id);
    
    // ✨ เพิ่ม: ดึงข้อมูลลูกหนี้
    $accountsReceivable = AccountReceivable::where('customer_id', $id)
        ->with(['payments'])
        ->orderBy('invoice_date', 'desc')
        ->get();
    
    return Inertia::render('Customers/Show', [
        'customer' => $customer,
        'accountsReceivable' => $accountsReceivable, // ✨ เพิ่ม
    ]);
}
```

### ไฟล์ที่ต้องแก้ไข
- `resources/js/Pages/Customers/Show.vue`
- `app/Http/Controllers/CustomerController.php`

---

## 🎯 จุดที่ 6: หน้าซัพพลายเออร์ (Suppliers)

### ตำแหน่ง
เพิ่มแท็บแสดงประวัติเจ้าหนี้ในหน้ารายละเอียดซัพพลายเออร์

### การแทรก

**คล้ายกับหน้า Customers แต่แสดง Accounts Payable แทน**

```vue
<!-- Tab: เจ้าหนี้ -->
<div v-if="activeTab === 'payables'">
  <h2>ประวัติเจ้าหนี้</h2>
  <table>
    <thead>
      <tr>
        <th>วันที่</th>
        <th>เลขที่ PO</th>
        <th>ยอดเงิน</th>
        <th>ชำระแล้ว</th>
        <th>คงเหลือ</th>
        <th>สถานะ</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="ap in accountsPayable" :key="ap.id">
        <!-- ... -->
      </tr>
    </tbody>
  </table>
</div>
```

### หมายเหตุ
ปัจจุบันระบบยังไม่มีหน้า Suppliers/Show.vue ต้องสร้างใหม่

---

## 🎯 จุดที่ 7: รายงาน (Reports)

### ตำแหน่ง
เพิ่มรายงานลูกหนี้-เจ้าหนี้ในเมนูรายงาน

### เมนูรายงานที่ต้องเพิ่ม

```
📊 รายงาน (Reports)
├── 📈 รายงานยอดขาย (Sales Report)
├── 📦 รายงานสินค้า (Product Report)
├── 📊 รายงานสต็อก (Inventory Report)
│
├── 💼 รายงานบัญชี ← ✨ เพิ่มใหม่
│   ├── 📥 รายงานลูกหนี้
│   │   ├── ลูกหนี้ทั้งหมด
│   │   ├── ลูกหนี้เกินกำหนด
│   │   └── รายงานอายุลูกหนี้ (Aging)
│   │
│   ├── 📤 รายงานเจ้าหนี้
│   │   ├── เจ้าหนี้ทั้งหมด
│   │   ├── เจ้าหนี้เกินกำหนด
│   │   └── รายงานอายุเจ้าหนี้ (Aging)
│   │
│   └── 💰 รายงานกระแสเงินสด
```

### ไฟล์ที่ต้องสร้าง/แก้ไข
- `resources/js/Pages/Reports/Accounts.vue` (สร้างใหม่)
- `app/Http/Controllers/AccountReportController.php` (สร้างใหม่)
- `routes/web.php` (เพิ่ม routes)

---

## 📁 สรุปไฟล์ที่ต้องสร้างใหม่

### Backend (Laravel)

```
app/
├── Http/Controllers/
│   ├── AccountReceivableController.php ← ✨ สร้างใหม่
│   ├── AccountPayableController.php    ← ✨ สร้างใหม่
│   ├── ARPaymentController.php         ← ✨ สร้างใหม่
│   ├── APPaymentController.php         ← ✨ สร้างใหม่
│   └── AccountReportController.php     ← ✨ สร้างใหม่
│
├── Models/
│   ├── AccountReceivable.php           ← ✨ สร้างใหม่
│   ├── AccountPayable.php              ← ✨ สร้างใหม่
│   ├── ARPayment.php                   ← ✨ สร้างใหม่
│   ├── APPayment.php                   ← ✨ สร้างใหม่
│   └── APItem.php                      ← ✨ สร้างใหม่
│
database/migrations/
├── xxxx_create_account_receivables_table.php    ← ✨ สร้างใหม่
├── xxxx_create_ar_payments_table.php            ← ✨ สร้างใหม่
├── xxxx_create_account_payables_table.php       ← ✨ สร้างใหม่
├── xxxx_create_ap_items_table.php               ← ✨ สร้างใหม่
└── xxxx_create_ap_payments_table.php            ← ✨ สร้างใหม่
```

### Frontend (Vue.js)

```
resources/js/Pages/
├── Accounts/                           ← ✨ สร้างโฟลเดอร์ใหม่
│   ├── Overview.vue                    ← ✨ หน้าภาพรวม
│   │
│   ├── Receivables/                    ← ✨ โฟลเดอร์ลูกหนี้
│   │   ├── Index.vue                   ← ✨ รายการลูกหนี้
│   │   ├── Show.vue                    ← ✨ รายละเอียดลูกหนี้
│   │   └── PaymentForm.vue             ← ✨ ฟอร์มรับชำระเงิน
│   │
│   └── Payables/                       ← ✨ โฟลเดอร์เจ้าหนี้
│       ├── Index.vue                   ← ✨ รายการเจ้าหนี้
│       ├── Create.vue                  ← ✨ บันทึกการซื้อเชื่อ
│       ├── Show.vue                    ← ✨ รายละเอียดเจ้าหนี้
│       └── PaymentForm.vue             ← ✨ ฟอร์มชำระเงิน
│
└── Reports/
    └── Accounts.vue                    ← ✨ รายงานบัญชี
```

---

## 🔗 Routes ที่ต้องเพิ่ม

### ใน `routes/web.php`

```php
// ✨ Accounts Management Routes
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Overview Dashboard
    Route::get('/accounts', [AccountController::class, 'overview'])
        ->name('accounts.overview');
    
    // Accounts Receivable (ลูกหนี้) - Admin, Manager, Accountant, Cashier
    Route::middleware(['role:admin,manager,accountant,cashier'])->group(function () {
        Route::get('/accounts-receivable', [AccountReceivableController::class, 'index'])
            ->name('accounts-receivable.index');
        Route::get('/accounts-receivable/{id}', [AccountReceivableController::class, 'show'])
            ->name('accounts-receivable.show');
        Route::post('/accounts-receivable', [AccountReceivableController::class, 'store'])
            ->name('accounts-receivable.store');
        Route::put('/accounts-receivable/{id}', [AccountReceivableController::class, 'update'])
            ->name('accounts-receivable.update');
        Route::delete('/accounts-receivable/{id}', [AccountReceivableController::class, 'destroy'])
            ->name('accounts-receivable.destroy');
        
        // AR Payments
        Route::post('/accounts-receivable/{id}/payment', [ARPaymentController::class, 'store'])
            ->name('ar-payments.store');
        Route::get('/accounts-receivable/{id}/payments', [ARPaymentController::class, 'index'])
            ->name('ar-payments.index');
    });
    
    // Accounts Payable (เจ้าหนี้) - Admin, Manager, Accountant only
    Route::middleware(['role:admin,manager,accountant'])->group(function () {
        Route::get('/accounts-payable', [AccountPayableController::class, 'index'])
            ->name('accounts-payable.index');
        Route::get('/accounts-payable/create', [AccountPayableController::class, 'create'])
            ->name('accounts-payable.create');
        Route::post('/accounts-payable', [AccountPayableController::class, 'store'])
            ->name('accounts-payable.store');
        Route::get('/accounts-payable/{id}', [AccountPayableController::class, 'show'])
            ->name('accounts-payable.show');
        Route::get('/accounts-payable/{id}/edit', [AccountPayableController::class, 'edit'])
            ->name('accounts-payable.edit');
        Route::put('/accounts-payable/{id}', [AccountPayableController::class, 'update'])
            ->name('accounts-payable.update');
        Route::delete('/accounts-payable/{id}', [AccountPayableController::class, 'destroy'])
            ->name('accounts-payable.destroy');
        
        // AP Payments
        Route::post('/accounts-payable/{id}/payment', [APPaymentController::class, 'store'])
            ->name('ap-payments.store');
        Route::get('/accounts-payable/{id}/payments', [APPaymentController::class, 'index'])
            ->name('ap-payments.index');
    });
    
    // Reports
    Route::middleware(['role:admin,manager,accountant'])->group(function () {
        Route::get('/reports/accounts', [AccountReportController::class, 'index'])
            ->name('reports.accounts');
        Route::get('/reports/accounts/ar-aging', [AccountReportController::class, 'arAging'])
            ->name('reports.ar-aging');
        Route::get('/reports/accounts/ap-aging', [AccountReportController::class, 'apAging'])
            ->name('reports.ap-aging');
        Route::get('/reports/accounts/cash-flow', [AccountReportController::class, 'cashFlow'])
            ->name('reports.cash-flow');
    });
});
```

---

## 🎨 การปรับแต่ง Navigation Menu

### ใน `resources/js/Layouts/AuthenticatedLayout.vue`

```vue
<template>
  <nav>
    <!-- ... existing menu items ... -->
    
    <!-- ✨ เพิ่มเมนูบัญชีลูกหนี้-เจ้าหนี้ -->
    <NavItem 
      v-if="hasRole(['admin', 'manager', 'accountant', 'cashier'])"
      :href="route('accounts.overview')"
      :active="route().current('accounts.*')"
    >
      <template #icon>
        <BanknotesIcon class="w-5 h-5" />
      </template>
      บัญชีลูกหนี้-เจ้าหนี้
    </NavItem>
    
    <!-- Dropdown สำหรับ Sub-menu -->
    <NavDropdown 
      v-if="hasRole(['admin', 'manager', 'accountant', 'cashier'])"
      label="บัญชีลูกหนี้-เจ้าหนี้"
    >
      <NavDropdownItem :href="route('accounts.overview')">
        ภาพรวม
      </NavDropdownItem>
      <NavDropdownItem :href="route('accounts-receivable.index')">
        ลูกหนี้
      </NavDropdownItem>
      <NavDropdownItem 
        v-if="hasRole(['admin', 'manager', 'accountant'])"
        :href="route('accounts-payable.index')"
      >
        เจ้าหนี้
      </NavDropdownItem>
    </NavDropdown>
    
    <!-- ... rest of menu items ... -->
  </nav>
</template>
```

---

## 📊 สรุปการแทรกระบบ

### ✅ จุดที่ต้องแทรก (7 จุด)

| # | จุดแทรก | ความสำคัญ | ความยาก | หมายเหตุ |
|---|---------|-----------|---------|----------|
| 1 | **เมนูหลัก** | 🔴 สูง | ⭐ ง่าย | เพิ่มเมนูใหม่ |
| 2 | **Dashboard** | 🟡 กลาง | ⭐⭐ ปานกลาง | เพิ่ม Widget |
| 3 | **หน้าลูกหนี้เดิม** | 🟡 กลาง | ⭐ ง่าย | Redirect หรือปรับปรุง |
| 4 | **POS** | 🔴 สูง | ⭐⭐⭐ ยาก | สร้าง AR อัตโนมัติ |
| 5 | **Customers** | 🟡 กลาง | ⭐⭐ ปานกลาง | เพิ่มแท็บ AR |
| 6 | **Suppliers** | 🟢 ต่ำ | ⭐⭐ ปานกลาง | เพิ่มแท็บ AP |
| 7 | **Reports** | 🟡 กลาง | ⭐⭐ ปานกลาง | เพิ่มรายงานใหม่ |

### 🎯 ลำดับการพัฒนาที่แนะนำ

```
Phase 1: Core System (Week 1-2)
├── สร้าง Database (Migrations)
├── สร้าง Models
├── สร้าง Controllers
└── สร้าง API Endpoints

Phase 2: AR Module (Week 3-4)
├── หน้า AR Index
├── หน้า AR Show
├── ฟอร์มรับชำระเงิน
└── แทรกใน POS (สร้าง AR อัตโนมัติ)

Phase 3: AP Module (Week 5-6)
├── หน้า AP Index
├── ฟอร์มบันทึกการซื้อเชื่อ
├── หน้า AP Show
└── ฟอร์มชำระเงิน

Phase 4: Integration (Week 7)
├── เพิ่มเมนูหลัก
├── เพิ่ม Widget ใน Dashboard
├── เพิ่มแท็บใน Customers
└── เพิ่มแท็บใน Suppliers

Phase 5: Reports & Polish (Week 8)
├── รายงานลูกหนี้
├── รายงานเจ้าหนี้
├── รายงานกระแสเงินสด
└── UI/UX Polish
```

---

## 🚨 ข้อควรระวัง

### 1. ความเข้ากันได้กับระบบเดิม
- ตรวจสอบว่า `customers` table มี `current_balance` field
- ตรวจสอบว่า `sales` table มี `payment_method` field
- ต้องมี `suppliers` table (ถ้ายังไม่มีต้องสร้าง)

### 2. Migration ข้อมูลเดิม
- ถ้ามีลูกหนี้เดิมใน `customers.current_balance` ต้อง migrate ไปยัง `account_receivables`
- สร้าง Seeder สำหรับข้อมูลทดสอบ

### 3. Permission & Roles
- ต้องเพิ่ม Role `accountant` ถ้ายังไม่มี
- ตรวจสอบ Permission ของแต่ละ Role

### 4. Validation
- ตรวจสอบว่า `due_date` ต้องไม่เป็นอดีต
- ตรวจสอบว่า `payment_amount` ไม่เกิน `remaining_amount`

---

## 📞 ขั้นตอนถัดไป

1. **Review PRD** - ตรวจสอบความต้องการ
2. **สร้าง Database** - Migrations
3. **สร้าง Models & Controllers** - Backend
4. **สร้าง UI** - Frontend
5. **Integration** - แทรกเข้าระบบเดิม
6. **Testing** - ทดสอบทุกฟีเจอร์
7. **Deployment** - Deploy production

---

**สรุป:** ระบบลูกหนี้-เจ้าหนี้สามารถแทรกเข้าไปในระบบ POS ปัจจุบันได้อย่างลงตัว โดยไม่ต้องแก้ไขโครงสร้างเดิมมาก เพียงแค่เพิ่มฟีเจอร์ใหม่และเชื่อมโยงกับระบบที่มีอยู่ 🎯
