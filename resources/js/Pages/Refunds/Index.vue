<template>
  <AppLayout title="รายการคืนสินค้า">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">รายการคืนสินค้า</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">ใบคืนทั้งหมด</div>
            <div class="text-2xl font-bold text-gray-800">{{ summary.total_refunds }}</div>
          </div>
          <div class="bg-yellow-50 rounded-lg shadow p-4">
            <div class="text-sm text-yellow-600">รอดำเนินการ</div>
            <div class="text-2xl font-bold text-yellow-700">{{ summary.pending_refunds }}</div>
          </div>
          <div class="bg-green-50 rounded-lg shadow p-4">
            <div class="text-sm text-green-600">เสร็จสิ้น</div>
            <div class="text-2xl font-bold text-green-700">{{ summary.completed_refunds }}</div>
          </div>
          <div class="bg-red-50 rounded-lg shadow p-4">
            <div class="text-sm text-red-600">ยอดคืนรวม</div>
            <div class="text-2xl font-bold text-red-700">฿{{ formatNumber(summary.total_refund_amount) }}</div>
          </div>
        </div>

        <!-- Filters & Actions -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
          <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
              <input
                v-model="localFilters.search"
                type="text"
                placeholder="ค้นหาเลขที่ใบคืน / เลขที่บิล..."
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                @keyup.enter="applyFilters"
              />
            </div>
            <div>
              <select
                v-model="localFilters.status"
                class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                @change="applyFilters"
              >
                <option value="">สถานะทั้งหมด</option>
                <option value="pending">รอดำเนินการ</option>
                <option value="approved">อนุมัติแล้ว</option>
                <option value="completed">เสร็จสิ้น</option>
                <option value="rejected">ปฏิเสธ</option>
              </select>
            </div>
            <div>
              <input
                v-model="localFilters.start_date"
                type="date"
                class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                @change="applyFilters"
              />
            </div>
            <div>
              <input
                v-model="localFilters.end_date"
                type="date"
                class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                @change="applyFilters"
              />
            </div>
            <Link
              :href="route('refunds.create')"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              สร้างใบคืนสินค้า
            </Link>
          </div>
        </div>

        <!-- Refunds Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">เลขที่ใบคืน</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">บิลอ้างอิง</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ลูกค้า</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">วันที่</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">ยอดคืน</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">สถานะ</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">จัดการ</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="refund in refunds.data" :key="refund.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <Link :href="route('refunds.show', refund.id)" class="text-blue-600 hover:underline font-medium">
                    {{ refund.refund_number }}
                  </Link>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ refund.sale?.sale_number }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  {{ refund.customer?.name || 'ลูกค้าทั่วไป' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(refund.refund_date) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-red-600">
                  ฿{{ formatNumber(refund.total_amount) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span :class="getStatusClass(refund.status)" class="px-2 py-1 text-xs rounded-full">
                    {{ getStatusLabel(refund.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex justify-center gap-2">
                    <Link
                      :href="route('refunds.show', refund.id)"
                      class="text-blue-600 hover:text-blue-800"
                      title="ดูรายละเอียด"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </Link>
                  </div>
                </td>
              </tr>
              <tr v-if="refunds.data.length === 0">
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                  ไม่พบรายการคืนสินค้า
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Pagination -->
          <div v-if="refunds.links && refunds.links.length > 3" class="px-6 py-4 border-t">
            <div class="flex justify-center gap-1">
              <template v-for="link in refunds.links" :key="link.label">
                <Link
                  v-if="link.url"
                  :href="link.url"
                  v-html="link.label"
                  class="px-3 py-1 border rounded text-sm"
                  :class="link.active ? 'bg-blue-600 text-white' : 'hover:bg-gray-100'"
                />
                <span v-else v-html="link.label" class="px-3 py-1 text-gray-400 text-sm" />
              </template>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  refunds: Object,
  summary: Object,
  filters: Object,
})

const localFilters = reactive({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
  start_date: props.filters?.start_date || '',
  end_date: props.filters?.end_date || '',
})

const applyFilters = () => {
  router.get(route('refunds.index'), localFilters, {
    preserveState: true,
    replace: true,
  })
}

const formatNumber = (num) => {
  return parseFloat(num || 0).toLocaleString('th-TH', { minimumFractionDigits: 2 })
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('th-TH', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
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
</script>
