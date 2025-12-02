<template>
  <AppLayout title="ใบเบิกสินค้า">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">ใบเบิกสินค้า</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Actions -->
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">รายการใบเบิกสินค้า</h1>
            <p class="text-gray-600">จัดการใบเบิกสินค้าและตัดสต็อก</p>
          </div>
          <div class="flex gap-2">
            <Link :href="route('reports.stock-requisitions')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
              📊 รายงาน
            </Link>
            <Link :href="route('stock-requisitions.create')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
              + สร้างใบเบิกใหม่
            </Link>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">ค้นหา</label>
              <input
                v-model="searchForm.search"
                type="text"
                placeholder="เลขที่ใบเบิก, ผู้เบิก..."
                class="w-full rounded-lg border-gray-300"
                @input="debouncedSearch"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">สถานะ</label>
              <select v-model="searchForm.status" @change="applyFilters" class="w-full rounded-lg border-gray-300">
                <option value="">ทั้งหมด</option>
                <option value="draft">ร่าง</option>
                <option value="completed">เสร็จสิ้น</option>
                <option value="cancelled">ยกเลิก</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">วันที่เริ่ม</label>
              <input v-model="searchForm.start_date" type="date" @change="applyFilters" class="w-full rounded-lg border-gray-300" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">วันที่สิ้นสุด</label>
              <input v-model="searchForm.end_date" type="date" @change="applyFilters" class="w-full rounded-lg border-gray-300" />
            </div>
          </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">เลขที่ใบเบิก</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">วันที่</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ผู้เบิก</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">แผนก/หน้างาน</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">จำนวนรายการ</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">มูลค่า (ทุน)</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">สถานะ</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">จัดการ</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="req in requisitions.data" :key="req.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <Link :href="route('stock-requisitions.show', req.id)" class="text-indigo-600 hover:text-indigo-900 font-medium">
                    {{ req.requisition_number }}
                  </Link>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(req.requisition_date) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ req.requester_name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ req.department || req.project_name || '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                  {{ req.total_items }} รายการ
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">
                  ฿{{ formatNumber(req.total_cost_amount) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span :class="getStatusClass(req.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                    {{ getStatusLabel(req.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex justify-center gap-2">
                    <Link :href="route('stock-requisitions.show', req.id)" class="text-blue-600 hover:text-blue-900" title="ดู">
                      👁️
                    </Link>
                    <Link v-if="req.status === 'draft'" :href="route('stock-requisitions.edit', req.id)" class="text-yellow-600 hover:text-yellow-900" title="แก้ไข">
                      ✏️
                    </Link>
                    <Link :href="route('stock-requisitions.print', req.id)" class="text-green-600 hover:text-green-900" title="พิมพ์">
                      🖨️
                    </Link>
                  </div>
                </td>
              </tr>
              <tr v-if="requisitions.data.length === 0">
                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                  ไม่พบข้อมูลใบเบิกสินค้า
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Pagination -->
          <div v-if="requisitions.last_page > 1" class="px-6 py-4 border-t border-gray-200">
            <div class="flex justify-between items-center">
              <div class="text-sm text-gray-500">
                แสดง {{ requisitions.from }} - {{ requisitions.to }} จาก {{ requisitions.total }} รายการ
              </div>
              <div class="flex gap-2">
                <Link
                  v-for="link in requisitions.links"
                  :key="link.label"
                  :href="link.url || '#'"
                  :class="[
                    'px-3 py-1 rounded text-sm',
                    link.active ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
                    !link.url ? 'opacity-50 cursor-not-allowed' : ''
                  ]"
                  v-html="link.label"
                />
              </div>
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
  requisitions: Object,
  filters: Object,
})

const searchForm = reactive({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
  start_date: props.filters?.start_date || '',
  end_date: props.filters?.end_date || '',
})

let searchTimeout = null

const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    applyFilters()
  }, 300)
}

const applyFilters = () => {
  router.get(route('stock-requisitions.index'), searchForm, {
    preserveState: true,
    preserveScroll: true,
  })
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('th-TH', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
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
</script>
