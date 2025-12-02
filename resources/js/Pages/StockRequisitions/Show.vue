<template>
  <AppLayout title="รายละเอียดใบเบิกสินค้า">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">รายละเอียดใบเบิกสินค้า</h2>
    </template>

    <div class="py-12">
      <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <!-- Flash Messages -->
        <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
          {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash?.error" class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
          {{ $page.props.flash.error }}
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
          <!-- Header -->
          <div class="flex justify-between items-start mb-6">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">{{ requisition.requisition_number }}</h1>
              <span :class="getStatusClass(requisition.status)" class="inline-block mt-2 px-3 py-1 text-sm font-medium rounded-full">
                {{ getStatusLabel(requisition.status) }}
              </span>
            </div>
            <div class="flex gap-2">
              <Link :href="route('stock-requisitions.index')" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                ← กลับ
              </Link>
              <Link :href="route('stock-requisitions.print', requisition.id)" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                🖨️ พิมพ์
              </Link>
              <Link v-if="requisition.status === 'draft'" :href="route('stock-requisitions.edit', requisition.id)" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                ✏️ แก้ไข
              </Link>
            </div>
          </div>

          <!-- Info Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 p-4 bg-gray-50 rounded-lg">
            <div>
              <p class="text-sm text-gray-500">วันที่เบิก</p>
              <p class="font-medium">{{ formatDate(requisition.requisition_date) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">ผู้เบิก</p>
              <p class="font-medium">{{ requisition.requester_name }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">แผนก</p>
              <p class="font-medium">{{ requisition.department || '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">โครงการ/หน้างาน</p>
              <p class="font-medium">{{ requisition.project_name || '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">ผู้สร้าง</p>
              <p class="font-medium">{{ requisition.created_by?.name || '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">วันที่สร้าง</p>
              <p class="font-medium">{{ formatDateTime(requisition.created_at) }}</p>
            </div>
            <div v-if="requisition.reason" class="md:col-span-2">
              <p class="text-sm text-gray-500">เหตุผลการเบิก</p>
              <p class="font-medium">{{ requisition.reason }}</p>
            </div>
          </div>

          <!-- Items Table -->
          <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4">รายการสินค้า</h3>
            <div class="border rounded-lg overflow-hidden">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ลำดับ</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">สินค้า</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">จำนวน</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">ราคาทุน</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">รวม</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="(item, index) in requisition.items" :key="item.id">
                    <td class="px-4 py-3 text-center text-gray-500">{{ index + 1 }}</td>
                    <td class="px-4 py-3">
                      <div class="font-medium">{{ item.product?.name }}</div>
                      <div class="text-sm text-gray-500">{{ item.product?.sku }}</div>
                    </td>
                    <td class="px-4 py-3 text-center">
                      {{ item.quantity }} {{ item.product?.unit || '' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                      ฿{{ formatNumber(item.cost_price) }}
                    </td>
                    <td class="px-4 py-3 text-right font-medium">
                      ฿{{ formatNumber(item.total_cost) }}
                    </td>
                  </tr>
                </tbody>
                <tfoot class="bg-gray-50">
                  <tr>
                    <td colspan="4" class="px-4 py-3 text-right font-semibold">รวมทั้งสิ้น:</td>
                    <td class="px-4 py-3 text-right font-bold text-lg text-indigo-600">
                      ฿{{ formatNumber(requisition.total_cost_amount) }}
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          <!-- Notes -->
          <div v-if="requisition.notes" class="mb-8 p-4 bg-yellow-50 rounded-lg">
            <p class="text-sm text-gray-500 mb-1">หมายเหตุ</p>
            <p>{{ requisition.notes }}</p>
          </div>

          <!-- Actions -->
          <div v-if="requisition.status === 'draft'" class="flex justify-end gap-4 pt-6 border-t">
            <button @click="showCancelModal = true" class="px-6 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50">
              ยกเลิกใบเบิก
            </button>
            <button @click="completeRequisition" :disabled="processing" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50">
              {{ processing ? 'กำลังดำเนินการ...' : '✓ ยืนยันเบิกสินค้า (ตัดสต็อก)' }}
            </button>
          </div>

          <!-- Cancelled Info -->
          <div v-if="requisition.status === 'cancelled'" class="mt-6 p-4 bg-red-50 rounded-lg">
            <p class="text-sm text-red-600 font-medium">ใบเบิกนี้ถูกยกเลิก</p>
            <p v-if="requisition.cancelled_reason" class="text-sm text-red-500 mt-1">เหตุผล: {{ requisition.cancelled_reason }}</p>
            <p class="text-sm text-gray-500 mt-1">เมื่อ: {{ formatDateTime(requisition.cancelled_at) }}</p>
          </div>

          <!-- Completed Info -->
          <div v-if="requisition.status === 'completed'" class="mt-6 p-4 bg-green-50 rounded-lg">
            <p class="text-sm text-green-600 font-medium">✓ เบิกสินค้าเรียบร้อยแล้ว (ตัดสต็อกแล้ว)</p>
            <p class="text-sm text-gray-500 mt-1">เมื่อ: {{ formatDateTime(requisition.completed_at) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Cancel Modal -->
    <div v-if="showCancelModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">ยกเลิกใบเบิกสินค้า</h3>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">เหตุผลการยกเลิก</label>
          <textarea v-model="cancelReason" rows="3" class="w-full rounded-lg border-gray-300" placeholder="ระบุเหตุผล (ไม่บังคับ)"></textarea>
        </div>
        <div class="flex justify-end gap-2">
          <button @click="showCancelModal = false" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            ปิด
          </button>
          <button @click="cancelRequisition" :disabled="processing" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50">
            ยืนยันยกเลิก
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  requisition: Object,
})

const processing = ref(false)
const showCancelModal = ref(false)
const cancelReason = ref('')

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('th-TH', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const formatDateTime = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleString('th-TH', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const formatNumber = (num) => {
  if (!num) return '0.00'
  return new Intl.NumberFormat('th-TH', { minimumFractionDigits: 2 }).format(num)
}

const getStatusLabel = (status) => {
  const labels = {
    draft: 'ร่าง',
    completed: 'เสร็จสิ้น',
    cancelled: 'ยกเลิก',
  }
  return labels[status] || status
}

const getStatusClass = (status) => {
  const classes = {
    draft: 'bg-yellow-100 text-yellow-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const completeRequisition = () => {
  if (!confirm('ยืนยันการเบิกสินค้า? ระบบจะตัดสต็อกตามรายการ')) return
  
  processing.value = true
  router.post(route('stock-requisitions.complete', props.requisition.id), {}, {
    onFinish: () => {
      processing.value = false
    },
  })
}

const cancelRequisition = () => {
  processing.value = true
  router.post(route('stock-requisitions.cancel', props.requisition.id), {
    reason: cancelReason.value,
  }, {
    onFinish: () => {
      processing.value = false
      showCancelModal.value = false
    },
  })
}
</script>
