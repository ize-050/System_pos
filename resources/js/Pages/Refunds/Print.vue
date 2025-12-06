<template>
  <div class="print-container p-8 max-w-2xl mx-auto bg-white">
    <!-- Header -->
    <div class="text-center mb-6 border-b pb-4">
      <h1 class="text-2xl font-bold">ใบคืนสินค้า / REFUND RECEIPT</h1>
      <p class="text-gray-600 mt-1">เลขที่: {{ refund.refund_number }}</p>
    </div>

    <!-- Info -->
    <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
      <div>
        <p><strong>บิลอ้างอิง:</strong> {{ refund.sale?.sale_number }}</p>
        <p><strong>ลูกค้า:</strong> {{ refund.customer?.name || 'ลูกค้าทั่วไป' }}</p>
      </div>
      <div class="text-right">
        <p><strong>วันที่คืน:</strong> {{ formatDate(refund.refund_date) }}</p>
        <p><strong>ผู้ทำรายการ:</strong> {{ refund.processed_by_user?.name }}</p>
      </div>
    </div>

    <!-- Reason -->
    <div v-if="refund.reason" class="mb-4 p-3 bg-gray-50 rounded">
      <strong>เหตุผล:</strong> {{ refund.reason }}
    </div>

    <!-- Items -->
    <table class="w-full mb-6 text-sm">
      <thead>
        <tr class="border-b-2">
          <th class="py-2 text-left">รายการ</th>
          <th class="py-2 text-center">จำนวน</th>
          <th class="py-2 text-right">ราคา</th>
          <th class="py-2 text-right">รวม</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in refund.items" :key="item.id" class="border-b">
          <td class="py-2">{{ item.product_name }}</td>
          <td class="py-2 text-center">{{ item.quantity }}</td>
          <td class="py-2 text-right">{{ formatNumber(item.unit_price) }}</td>
          <td class="py-2 text-right">{{ formatNumber(item.total_price) }}</td>
        </tr>
      </tbody>
    </table>

    <!-- Totals -->
    <div class="flex justify-end mb-6">
      <div class="w-48">
        <div class="flex justify-between py-1">
          <span>ยอดรวม:</span>
          <span>฿{{ formatNumber(refund.subtotal) }}</span>
        </div>
        <div v-if="refund.tax_amount > 0" class="flex justify-between py-1">
          <span>ภาษี:</span>
          <span>฿{{ formatNumber(refund.tax_amount) }}</span>
        </div>
        <div class="flex justify-between py-2 border-t-2 font-bold text-lg">
          <span>ยอดคืน:</span>
          <span>฿{{ formatNumber(refund.total_amount) }}</span>
        </div>
      </div>
    </div>

    <!-- Refund Method -->
    <div class="text-center mb-6 p-3 bg-gray-100 rounded">
      <strong>วิธีคืนเงิน:</strong> {{ getRefundMethodLabel(refund.refund_method) }}
    </div>

    <!-- Signatures -->
    <div class="grid grid-cols-2 gap-8 mt-12">
      <div class="text-center">
        <div class="border-t border-gray-400 pt-2 mt-16">
          <p>ผู้รับคืนสินค้า</p>
        </div>
      </div>
      <div class="text-center">
        <div class="border-t border-gray-400 pt-2 mt-16">
          <p>ลูกค้า</p>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="text-center text-xs text-gray-500 mt-8 pt-4 border-t">
      <p>พิมพ์เมื่อ: {{ new Date().toLocaleString('th-TH') }}</p>
    </div>

    <!-- Print Button (hidden when printing) -->
    <div class="no-print mt-8 text-center">
      <button @click="printPage" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        พิมพ์
      </button>
      <button @click="goBack" class="px-6 py-2 ml-2 border border-gray-300 rounded-lg hover:bg-gray-50">
        กลับ
      </button>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
  refund: Object,
})

const formatNumber = (num) => {
  return parseFloat(num || 0).toLocaleString('th-TH', { minimumFractionDigits: 2 })
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('th-TH', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const getRefundMethodLabel = (method) => {
  const labels = {
    cash: 'เงินสด',
    transfer: 'โอนเงิน',
    credit_card: 'บัตรเครดิต',
    store_credit: 'เครดิตร้าน',
  }
  return labels[method] || method
}

const printPage = () => {
  window.print()
}

const goBack = () => {
  router.visit(route('refunds.show', props.refund.id))
}
</script>

<style>
@media print {
  .no-print {
    display: none !important;
  }
  
  body {
    print-color-adjust: exact;
    -webkit-print-color-adjust: exact;
  }
  
  @page {
    size: A4;
    margin: 15mm;
  }
}
</style>
