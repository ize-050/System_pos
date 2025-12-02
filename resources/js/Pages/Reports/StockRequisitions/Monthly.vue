<template>
  <AppLayout title="รายงานการเบิกสินค้ารายเดือน">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">รายงานการเบิกสินค้ารายเดือน</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">รายงานการเบิกสินค้ารายเดือน</h1>
            <p class="text-gray-600">{{ formatMonthThai(month) }}</p>
          </div>
          <Link :href="route('reports.stock-requisitions')" class="text-gray-600 hover:text-gray-900">
            ← กลับ
          </Link>
        </div>

        <!-- Month Picker -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
          <div class="flex items-center gap-4">
            <label class="text-sm font-medium text-gray-700">เลือกเดือน:</label>
            <input v-model="selectedMonth" type="month" @change="changeMonth" class="rounded-lg border-gray-300" />
          </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="text-sm text-gray-500 mb-1">จำนวนใบเบิก</div>
            <div class="text-3xl font-bold text-indigo-600">{{ summary.total_requisitions }}</div>
          </div>
          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="text-sm text-gray-500 mb-1">มูลค่ารวม (ราคาทุน)</div>
            <div class="text-3xl font-bold text-green-600">฿{{ formatNumber(summary.total_cost) }}</div>
          </div>
          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="text-sm text-gray-500 mb-1">จำนวนสินค้า</div>
            <div class="text-3xl font-bold text-blue-600">{{ summary.total_items }}</div>
          </div>
          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="text-sm text-gray-500 mb-1">เฉลี่ย/วัน</div>
            <div class="text-3xl font-bold text-purple-600">฿{{ formatNumber(summary.avg_per_day) }}</div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Daily Chart -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">📈 มูลค่าการเบิกรายวัน</h3>
            <div class="h-64">
              <div class="flex items-end h-48 gap-1">
                <div
                  v-for="day in dailyData"
                  :key="day.date"
                  class="flex-1 bg-indigo-500 hover:bg-indigo-600 transition-colors cursor-pointer rounded-t"
                  :style="{ height: getBarHeight(day.total_cost) + '%' }"
                  :title="`${day.date}: ฿${formatNumber(day.total_cost)}`"
                ></div>
              </div>
              <div class="text-center text-sm text-gray-500 mt-2">วันในเดือน</div>
            </div>
          </div>

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
            </div>
          </div>

          <!-- By Department -->
          <div class="bg-white rounded-lg shadow-sm p-6 lg:col-span-2">
            <h3 class="text-lg font-semibold mb-4">📊 แยกตามแผนก/หน้างาน</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div v-for="dept in byDepartment" :key="dept.department" class="p-4 bg-gray-50 rounded-lg">
                <div class="font-medium">{{ dept.department || 'ไม่ระบุ' }}</div>
                <div class="text-sm text-gray-500">{{ dept.count }} ใบเบิก</div>
                <div class="text-lg font-bold text-indigo-600">฿{{ formatNumber(dept.total_cost) }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  month: String,
  dailyData: Array,
  summary: Object,
  topProducts: Array,
  byDepartment: Array,
})

const selectedMonth = ref(props.month)

const changeMonth = () => {
  router.get(route('reports.stock-requisitions.monthly'), { month: selectedMonth.value })
}

const maxDailyValue = computed(() => {
  return Math.max(...props.dailyData.map(d => parseFloat(d.total_cost) || 0), 1)
})

const getBarHeight = (value) => {
  return Math.max((parseFloat(value) / maxDailyValue.value) * 100, 2)
}

const formatMonthThai = (month) => {
  const [year, m] = month.split('-')
  const date = new Date(year, parseInt(m) - 1, 1)
  return date.toLocaleDateString('th-TH', { year: 'numeric', month: 'long' })
}

const formatNumber = (num) => {
  if (!num) return '0.00'
  return new Intl.NumberFormat('th-TH', { minimumFractionDigits: 2 }).format(num)
}
</script>
