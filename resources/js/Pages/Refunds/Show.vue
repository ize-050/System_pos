<template>
  <AppLayout title="รายละเอียดใบคืนสินค้า">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">รายละเอียดใบคืนสินค้า</h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Flash Messages -->
        <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
          {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash?.error" class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
          {{ $page.props.flash.error }}
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
          <!-- Header -->
          <div class="p-6 border-b">
            <div class="flex justify-between items-start">
              <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ refund.refund_number }}</h1>
                <p class="text-gray-500 mt-1">
                  บิลอ้างอิง: 
                  <Link :href="route('sales.show', refund.sale_id)" class="text-blue-600 hover:underline">
                    {{ refund.sale?.sale_number }}
                  </Link>
                </p>
              </div>
              <span :class="getStatusClass(refund.status)" class="px-4 py-2 rounded-full text-sm font-medium">
                {{ getStatusLabel(refund.status) }}
              </span>
            </div>
          </div>

          <!-- Info Grid -->
          <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4 bg-gray-50">
            <div>
              <div class="text-sm text-gray-500">วันที่คืน</div>
              <div class="font-medium">{{ formatDate(refund.refund_date) }}</div>
            </div>
            <div>
              <div class="text-sm text-gray-500">ลูกค้า</div>
              <div class="font-medium">{{ refund.customer?.name || 'ลูกค้าทั่วไป' }}</div>
            </div>
            <div>
              <div class="text-sm text-gray-500">วิธีคืนเงิน</div>
              <div class="font-medium">{{ getRefundMethodLabel(refund.refund_method) }}</div>
            </div>
            <div>
              <div class="text-sm text-gray-500">ผู้ทำรายการ</div>
              <div class="font-medium">{{ refund.processed_by_user?.name || '-' }}</div>
            </div>
          </div>

          <!-- Reason -->
          <div v-if="refund.reason" class="p-6 border-t">
            <div class="text-sm text-gray-500 mb-1">เหตุผลการคืน</div>
            <div class="font-medium text-gray-900">{{ refund.reason }}</div>
          </div>

          <!-- Items Table -->
          <div class="p-6 border-t">
            <h3 class="font-medium text-gray-900 mb-4">รายการสินค้าที่คืน</h3>
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">สินค้า</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">จำนวน</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">ราคา/หน่วย</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">รวม</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">สภาพ</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">คืนสต็อก</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="item in refund.items" :key="item.id">
                  <td class="px-4 py-3">
                    <div class="font-medium">{{ item.product_name }}</div>
                  </td>
                  <td class="px-4 py-3 text-center">{{ item.quantity }}</td>
                  <td class="px-4 py-3 text-right">฿{{ formatNumber(item.unit_price) }}</td>
                  <td class="px-4 py-3 text-right font-medium">฿{{ formatNumber(item.total_price) }}</td>
                  <td class="px-4 py-3 text-center">
                    <span :class="getConditionClass(item.condition)" class="px-2 py-1 text-xs rounded">
                      {{ getConditionLabel(item.condition) }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <span v-if="item.return_to_stock" class="text-green-600">✓</span>
                    <span v-else class="text-gray-400">-</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Totals -->
          <div class="p-6 border-t bg-gray-50">
            <div class="flex justify-end">
              <div class="w-64 space-y-2">
                <div class="flex justify-between text-sm">
                  <span>ยอดรวม:</span>
                  <span>฿{{ formatNumber(refund.subtotal) }}</span>
                </div>
                <div v-if="refund.tax_amount > 0" class="flex justify-between text-sm">
                  <span>ภาษี:</span>
                  <span>฿{{ formatNumber(refund.tax_amount) }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold border-t pt-2">
                  <span>ยอดคืนทั้งหมด:</span>
                  <span class="text-red-600">฿{{ formatNumber(refund.total_amount) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div v-if="refund.notes" class="p-6 border-t">
            <div class="text-sm text-gray-500 mb-1">หมายเหตุ</div>
            <div class="text-gray-700 whitespace-pre-line">{{ refund.notes }}</div>
          </div>

          <!-- Actions -->
          <div class="p-6 border-t flex justify-between items-center">
            <Link :href="route('refunds.index')" class="text-gray-600 hover:text-gray-800">
              ← กลับไปรายการ
            </Link>
            
            <div class="flex gap-2">
              <!-- Print -->
              <button
                @click="printRefund"
                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center gap-2"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                พิมพ์
              </button>

              <!-- Approve (for pending) -->
              <button
                v-if="refund.status === 'pending'"
                @click="approveRefund"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
              >
                อนุมัติ
              </button>

              <!-- Complete (for pending or approved) -->
              <button
                v-if="refund.status === 'pending' || refund.status === 'approved'"
                @click="completeRefund"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
              >
                ดำเนินการคืนเงิน
              </button>

              <!-- Reject (for pending) -->
              <button
                v-if="refund.status === 'pending'"
                @click="rejectRefund"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
              >
                ปฏิเสธ
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

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
    hour: '2-digit',
    minute: '2-digit',
  })
}

const getStatusLabel = (status) => {
  const labels = {
    pending: 'รอดำเนินการ',
    approved: 'อนุมัติแล้ว',
    completed: 'เสร็จสิ้น',
    rejected: 'ปฏิเสธ',
  }
  return labels[status] || status
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
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

const getConditionLabel = (condition) => {
  const labels = {
    good: 'สภาพดี',
    damaged: 'เสียหาย',
    defective: 'ชำรุด',
  }
  return labels[condition] || condition || '-'
}

const getConditionClass = (condition) => {
  const classes = {
    good: 'bg-green-100 text-green-800',
    damaged: 'bg-orange-100 text-orange-800',
    defective: 'bg-red-100 text-red-800',
  }
  return classes[condition] || 'bg-gray-100 text-gray-800'
}

const printRefund = () => {
  window.open(route('refunds.print', props.refund.id), '_blank')
}

const approveRefund = () => {
  if (confirm('ยืนยันการอนุมัติใบคืนสินค้านี้?')) {
    router.post(route('refunds.approve', props.refund.id))
  }
}

const completeRefund = () => {
  if (confirm('ยืนยันการดำเนินการคืนเงินและคืนสต็อก?')) {
    router.post(route('refunds.complete', props.refund.id))
  }
}

const rejectRefund = () => {
  const reason = prompt('กรุณาระบุเหตุผลในการปฏิเสธ:')
  if (reason !== null) {
    router.post(route('refunds.reject', props.refund.id), { reject_reason: reason })
  }
}
</script>
