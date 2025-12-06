<template>
  <AppLayout title="Purchase Order Details">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight no-print">รายละเอียดใบสั่งซื้อ</h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg p-6">
          <!-- Header สำหรับพิมพ์ -->
          <div class="print-header mb-8">
            <div class="text-center mb-6">
              <!-- Logo -->
              <div v-if="settings?.show_logo && logoUrl" class="mb-4 flex justify-center">
                <img :src="logoUrl" alt="Shop Logo" class="h-20 object-contain" />
              </div>
              
              <h1 class="text-3xl font-bold text-gray-800 mb-2">ใบสั่งซื้อสินค้า</h1>
              <h2 class="text-xl text-gray-600">PURCHASE ORDER</h2>
            </div>
            
            <div class="grid grid-cols-2 gap-8 mb-6">
              <!-- ข้อมูลบริษัท -->
              <div class="border-2 border-gray-300 p-4 rounded">
                <h3 class="font-bold text-lg mb-2 text-gray-700">จาก / FROM:</h3>
                <p class="font-semibold text-lg">{{ settings?.shop_name || 'ร้านค้า POS' }}</p>
                <p v-if="settings?.shop_address" class="text-sm text-gray-600">{{ settings.shop_address }}</p>
                <template v-else>
                  <p class="text-sm text-gray-600">123 ถนนสุขุมวิท</p>
                  <p class="text-sm text-gray-600">แขวงคลองเตย เขตคลองเตย</p>
                  <p class="text-sm text-gray-600">กรุงเทพฯ 10110</p>
                </template>
                <p v-if="settings?.shop_phone" class="text-sm text-gray-600 mt-2">โทร: {{ settings.shop_phone }}</p>
                <p v-if="settings?.tax_id" class="text-sm text-gray-600">เลขประจำตัวผู้เสียภาษี: {{ settings.tax_id }}</p>
              </div>
              
              <!-- ข้อมูลซัพพลายเออร์ -->
              <div class="border-2 border-gray-300 p-4 rounded">
                <h3 class="font-bold text-lg mb-2 text-gray-700">ถึง / TO:</h3>
                <p class="font-semibold text-lg">{{ purchaseOrder.supplier?.name }}</p>
                <p class="text-sm text-gray-600">{{ purchaseOrder.supplier?.contact_person }}</p>
                <p class="text-sm text-gray-600">{{ purchaseOrder.supplier?.address }}</p>
                <p class="text-sm text-gray-600 mt-2">โทร: {{ purchaseOrder.supplier?.phone }}</p>
                <p class="text-sm text-gray-600">อีเมล: {{ purchaseOrder.supplier?.email }}</p>
              </div>
            </div>
            
            <!-- ข้อมูล PO -->
            <div class="grid grid-cols-3 gap-4 mb-6 bg-gray-50 p-4 rounded">
              <div>
                <p class="text-sm text-gray-600">เลขที่ใบสั่งซื้อ / PO Number:</p>
                <p class="font-bold text-lg text-blue-600">{{ purchaseOrder.po_number }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">วันที่สั่งซื้อ / Order Date:</p>
                <p class="font-semibold">{{ formatDate(purchaseOrder.order_date) }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">วันที่คาดว่าจะได้รับ / Expected Date:</p>
                <p class="font-semibold">{{ purchaseOrder.expected_delivery_date ? formatDate(purchaseOrder.expected_delivery_date) : '-' }}</p>
              </div>
            </div>
            
            <!-- สถานะ (แสดงเฉพาะบนหน้าจอ) -->
            <div class="no-print mb-4">
              <span :class="getStatusClass(purchaseOrder.status)" class="px-4 py-2 rounded-full text-sm font-semibold">
                {{ getStatusText(purchaseOrder.status) }}
              </span>
            </div>
          </div>

          <!-- Items Table -->
          <div class="mb-6">
            <h3 class="font-bold text-lg mb-3 text-gray-700">รายการสินค้า / ITEMS</h3>
            <table class="min-w-full border-2 border-gray-300">
              <thead class="bg-gray-100">
                <tr>
                  <th class="border border-gray-300 px-4 py-3 text-center text-sm font-bold text-gray-700">ลำดับ<br/>No.</th>
                  <th class="border border-gray-300 px-4 py-3 text-left text-sm font-bold text-gray-700">รายการสินค้า<br/>Description</th>
                  <th class="border border-gray-300 px-4 py-3 text-center text-sm font-bold text-gray-700">จำนวน<br/>Quantity</th>
                  <th class="border border-gray-300 px-4 py-3 text-right text-sm font-bold text-gray-700">ราคา/หน่วย<br/>Unit Price</th>
                  <th class="border border-gray-300 px-4 py-3 text-right text-sm font-bold text-gray-700">จำนวนเงิน<br/>Amount</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in purchaseOrder.items" :key="item.id">
                  <td class="border border-gray-300 px-4 py-2 text-center">{{ index + 1 }}</td>
                  <td class="border border-gray-300 px-4 py-2">{{ item.product_name }}</td>
                  <td class="border border-gray-300 px-4 py-2 text-center">{{ item.quantity_ordered }}</td>
                  <td class="border border-gray-300 px-4 py-2 text-right">{{ formatCurrency(item.unit_price) }}</td>
                  <td class="border border-gray-300 px-4 py-2 text-right font-semibold">{{ formatCurrency(item.total_price) }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Totals -->
          <div class="flex justify-end mb-8">
            <div class="w-96 border-2 border-gray-300 p-4">
              <div class="flex justify-between py-2 text-base">
                <span class="font-semibold">ยอดรวม / Subtotal:</span>
                <span class="font-semibold">{{ formatCurrency(purchaseOrder.subtotal) }}</span>
              </div>
              <div class="flex justify-between py-2 text-base border-t">
                <span class="font-semibold">ส่วนลด / Discount:</span>
                <span class="font-semibold">{{ formatCurrency(purchaseOrder.discount_amount || 0) }}</span>
              </div>
              <div class="flex justify-between py-2 text-base border-t">
                <span class="font-semibold">ภาษีมูลค่าเพิ่ม 7% / VAT 7%:</span>
                <span class="font-semibold">{{ formatCurrency(purchaseOrder.vat_amount) }}</span>
              </div>
              <div class="flex justify-between py-3 text-lg font-bold border-t-2 border-gray-400 bg-gray-50">
                <span>ยอดรวมสุทธิ / Grand Total:</span>
                <span class="text-blue-600">{{ formatCurrency(purchaseOrder.total_amount) }}</span>
              </div>
            </div>
          </div>
          
          <!-- หมายเหตุ -->
          <div v-if="purchaseOrder.notes" class="mb-6 border-2 border-gray-300 p-4">
            <h3 class="font-bold text-gray-700 mb-2">หมายเหตุ / Notes:</h3>
            <p class="text-gray-600">{{ purchaseOrder.notes }}</p>
          </div>
          
          <!-- ลายเซ็น -->
          <div class="grid grid-cols-2 gap-8 mt-12 print-only">
            <div class="text-center">
              <div class="border-t-2 border-gray-400 pt-2 mt-16">
                <p class="font-semibold">ผู้สั่งซื้อ / Authorized By</p>
                <p class="text-sm text-gray-600">วันที่ / Date: _______________</p>
              </div>
            </div>
            <div class="text-center">
              <div class="border-t-2 border-gray-400 pt-2 mt-16">
                <p class="font-semibold">ผู้อนุมัติ / Approved By</p>
                <p class="text-sm text-gray-600">วันที่ / Date: _______________</p>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-2">
            <Link :href="route('purchase-orders.index')" class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400">กลับ</Link>
            
            <!-- Print button -->
            <button 
              @click="printPO" 
              class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 flex items-center gap-2"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
              </svg>
              พิมพ์ใบสั่งซื้อ
            </button>
            
            <!-- Edit button - only for draft status -->
            <Link 
              v-if="purchaseOrder.status === 'draft'" 
              :href="route('purchase-orders.edit', purchaseOrder.id)" 
              class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600"
            >
              แก้ไข
            </Link>
            
            <!-- Delete button - only for draft status -->
            <button 
              v-if="purchaseOrder.status === 'draft'" 
              @click="deletePO" 
              class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
            >
              ลบ
            </button>
            
            <!-- Send Order button -->
            <button 
              v-if="purchaseOrder.status === 'draft'" 
              @click="sendOrder" 
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              ส่งใบสั่งซื้อ
            </button>
            
            <!-- Receive Goods button -->
            <button 
              v-if="['pending', 'shipping'].includes(purchaseOrder.status)" 
              @click="receiveGoods" 
              class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
            >
              รับสินค้า
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed } from 'vue';

const props = defineProps({
  purchaseOrder: Object,
  settings: Object,
});

const logoUrl = computed(() => {
  if (props.settings?.logo_path) {
    return `/storage/${props.settings.logo_path}`;
  }
  return null;
});

const formatDate = (date) => new Date(date).toLocaleDateString('th-TH');

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('th-TH', { style: 'currency', currency: 'THB' }).format(amount || 0);
};

const getStatusClass = (status) => {
  const classes = { 
    draft: 'bg-gray-100 text-gray-800', 
    pending: 'bg-yellow-100 text-yellow-800', 
    shipping: 'bg-blue-100 text-blue-800', 
    received: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusText = (status) => {
  const statusText = {
    draft: 'ฉบับร่าง',
    pending: 'รอดำเนินการ',
    shipping: 'กำลังจัดส่ง',
    received: 'รับสินค้าแล้ว',
    cancelled: 'ยกเลิก'
  };
  return statusText[status] || status;
};

const sendOrder = () => {
  if (confirm('คุณต้องการส่งใบสั่งซื้อนี้ใช่หรือไม่?')) {
    router.post(route('purchase-orders.send', props.purchaseOrder.id));
  }
};

const receiveGoods = () => {
  router.get(route('purchase-orders.receive', props.purchaseOrder.id));
};

const deletePO = () => {
  if (confirm('คุณต้องการลบใบสั่งซื้อนี้ใช่หรือไม่? การกระทำนี้ไม่สามารถย้อนกลับได้')) {
    router.delete(route('purchase-orders.destroy', props.purchaseOrder.id));
  }
};

const printPO = () => {
  window.print();
};
</script>

<style>
/* แสดงเฉพาะเมื่อพิมพ์ */
.print-only {
  display: none;
}

@media print {
  /* ซ่อนส่วนที่ไม่ต้องการพิมพ์ */
  nav, header, button, .no-print, aside, footer, a {
    display: none !important;
  }
  
  /* แสดงส่วนที่ต้องการพิมพ์ */
  .print-only {
    display: block !important;
  }
  
  /* ปรับแต่งหน้ากระดาษ - ลด margin ให้อยู่หน้าเดียว */
  @page {
    size: A4;
    margin: 8mm;
  }
  
  body {
    print-color-adjust: exact;
    -webkit-print-color-adjust: exact;
    background: white !important;
    font-size: 11px !important;
  }
  
  /* ปรับ container */
  .max-w-7xl {
    max-width: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
  }
  
  .py-12 {
    padding: 0 !important;
  }
  
  .p-6 {
    padding: 8px !important;
  }
  
  /* ทำให้ตารางดูดีขึ้นเมื่อพิมพ์ */
  table {
    page-break-inside: avoid;
    border-collapse: collapse !important;
    font-size: 10px !important;
  }
  
  th, td {
    padding: 4px 6px !important;
  }
  
  /* แสดงเฉพาะเนื้อหาหลัก */
  .bg-white {
    box-shadow: none !important;
    border-radius: 0 !important;
  }
  
  /* ปรับ font size ให้เล็กลง */
  .print-header h1 {
    font-size: 20px !important;
    margin-bottom: 2px !important;
  }
  
  .print-header h2 {
    font-size: 14px !important;
  }
  
  .print-header .mb-6 {
    margin-bottom: 8px !important;
  }
  
  .print-header .mb-8 {
    margin-bottom: 10px !important;
  }
  
  /* ลดขนาด grid gap */
  .grid.gap-8 {
    gap: 12px !important;
  }
  
  .grid.gap-4 {
    gap: 8px !important;
  }
  
  /* ลดขนาด padding ของ box */
  .border-2.p-4 {
    padding: 8px !important;
  }
  
  /* ปรับ border */
  .border-2 {
    border-width: 1px !important;
  }
  
  /* ปรับสี background */
  .bg-gray-50, .bg-gray-100 {
    background-color: #f9fafb !important;
  }
  
  /* ลดขนาด text */
  .text-lg {
    font-size: 12px !important;
  }
  
  .text-xl {
    font-size: 14px !important;
  }
  
  .text-3xl {
    font-size: 18px !important;
  }
  
  .text-sm {
    font-size: 9px !important;
  }
  
  .text-base {
    font-size: 10px !important;
  }
  
  /* ลดขนาดลายเซ็น */
  .mt-12 {
    margin-top: 20px !important;
  }
  
  .mt-16 {
    margin-top: 30px !important;
  }
  
  /* ลด margin bottom */
  .mb-6 {
    margin-bottom: 8px !important;
  }
  
  .mb-8 {
    margin-bottom: 10px !important;
  }
  
  /* Logo size */
  .h-20 {
    height: 50px !important;
  }
  
  /* ยอดรวม */
  .w-96 {
    width: 250px !important;
  }
}
</style>
