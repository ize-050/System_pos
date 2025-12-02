<template>
  <AppLayout title="รายงานการเบิกสินค้า">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">รายงานการเบิกสินค้า</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
          <h1 class="text-2xl font-bold text-gray-900">รายงานการเบิกสินค้า</h1>
          <div class="flex gap-2">
            <Link :href="route('reports.stock-requisitions.daily')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
              📅 รายวัน
            </Link>
            <Link :href="route('reports.stock-requisitions.monthly')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
              📆 รายเดือน
            </Link>
            <Link :href="route('reports.stock-requisitions.yearly')" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
              📊 รายปี
            </Link>
          </div>
        </div>

        <!-- Date Filter -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">วันที่เริ่ม</label>
              <input v-model="filterForm.start_date" type="date" @change="applyFilters" class="w-full rounded-lg border-gray-300" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">วันที่สิ้นสุด</label>
              <input v-model="filterForm.end_date" type="date" @change="applyFilters" class="w-full rounded-lg border-gray-300" />
            </div>
            <div class="flex items-end">
              <button @click="applyFilters" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                🔍 ค้นหา
              </button>
            </div>
          </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="text-sm text-gray-500 mb-1">จำนวนใบเบิก (เสร็จสิ้น)</div>
            <div class="text-3xl font-bold text-indigo-600">{{ summary.total_requisitions }}</div>
          </div>
          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="text-sm text-gray-500 mb-1">มูลค่ารวม (ราคาทุน)</div>
            <div class="text-3xl font-bold text-green-600">฿{{ formatNumber(summary.total_cost) }}</div>
          </div>
          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="text-sm text-gray-500 mb-1">จำนวนสินค้าที่เบิก</div>
            <div class="text-3xl font-bold text-blue-600">{{ summary.total_items }}</div>
          </div>
          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="text-sm text-gray-500 mb-1">ร่าง / ยกเลิก</div>
            <div class="text-3xl font-bold text-gray-600">{{ summary.draft_count }} / {{ summary.cancelled_count }}</div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Top Products -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">🏆 สินค้าที่เบิกมากที่สุด</h3>
            <div class="space-y-3">
              <div v-for="(item, index) in topProducts" :key="item.product_id" class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center gap-3">
                  <span class="w-8 h-8 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-full font-bold">{{ index + 1 }}</span>
                  <div>
                    <div class="font-medium">{{ item.product?.name }}</div>
                    <div class="text-sm text-gray-500">{{ item.product?.sku }}</div>
                  </div>
                </div>
                <div class="text-right">
                  <div class="font-bold">{{ item.total_quantity }} {{ item.product?.unit || '' }}</div>
                  <div class="text-sm text-gray-500">฿{{ formatNumber(item.total_cost) }}</div>
                </div>
              </div>
              <div v-if="topProducts.length === 0" class="text-center text-gray-500 py-4">
                ไม่มีข้อมูล
              </div>
            </div>
          </div>

          <!-- Recent Requisitions -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">📋 ใบเบิกล่าสุด</h3>
            <div class="space-y-3">
              <div v-for="req in recentRequisitions" :key="req.id" class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <div>
                  <Link :href="route('stock-requisitions.show', req.id)" class="font-medium text-indigo-600 hover:text-indigo-800">
                    {{ req.requisition_number }}
                  </Link>
                  <div class="text-sm text-gray-500">{{ req.requester_name }}</div>
                </div>
                <div class="text-right">
                  <span :class="getStatusClass(req.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                    {{ getStatusLabel(req.status) }}
                  </span>
                  <div class="text-sm text-gray-500 mt-1">฿{{ formatNumber(req.total_cost_amount) }}</div>
                </div>
              </div>
              <div v-if="recentRequisitions.length === 0" class="text-center text-gray-500 py-4">
                ไม่มีข้อมูล
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  summary: Object,
  topProducts: Array,
  recentRequisitions: Array,
  filters: Object,
})

const filterForm = reactive({
  start_date: props.filters?.start_date || '',
  end_date: props.filters?.end_date || '',
})

const applyFilters = () => {
  router.get(route('reports.stock-requisitions'), filterForm, {
    preserveState: true,
  })
}

const formatNumber = (num) => {
  if (!num) return '0.00'
  return new Intl.NumberFormat('th-TH', { minimumFractionDigits: 2 }).format(num)
}

const getStatusLabel = (status) => {
  const labels = { draft: 'ร่าง', completed: 'เสร็จสิ้น', cancelled: 'ยกเลิก' }
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
