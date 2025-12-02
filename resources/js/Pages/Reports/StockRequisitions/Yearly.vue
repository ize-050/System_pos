<template>
  <AppLayout title="รายงานการเบิกสินค้ารายปี">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">รายงานการเบิกสินค้ารายปี</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">รายงานการเบิกสินค้ารายปี</h1>
            <p class="text-gray-600">ปี พ.ศ. {{ parseInt(year) + 543 }}</p>
          </div>
          <Link :href="route('reports.stock-requisitions')" class="text-gray-600 hover:text-gray-900">
            ← กลับ
          </Link>
        </div>

        <!-- Year Picker -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
          <div class="flex items-center gap-4">
            <label class="text-sm font-medium text-gray-700">เลือกปี:</label>
            <select v-model="selectedYear" @change="changeYear" class="rounded-lg border-gray-300">
              <option v-for="y in availableYears" :key="y" :value="y">{{ y + 543 }}</option>
            </select>
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
            <div class="text-sm text-gray-500 mb-1">เฉลี่ย/เดือน</div>
            <div class="text-3xl font-bold text-purple-600">฿{{ formatNumber(summary.avg_per_month) }}</div>
          </div>
        </div>

        <!-- Comparison with Last Year -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
          <h3 class="text-lg font-semibold mb-4">📊 เปรียบเทียบกับปีก่อน</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
              <div class="text-sm text-gray-500">ปีนี้</div>
              <div class="text-2xl font-bold text-indigo-600">฿{{ formatNumber(summary.total_cost) }}</div>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
              <div class="text-sm text-gray-500">ปีก่อน</div>
              <div class="text-2xl font-bold text-gray-600">฿{{ formatNumber(comparison.last_year_total) }}</div>
            </div>
            <div class="text-center p-4 rounded-lg" :class="comparison.difference >= 0 ? 'bg-red-50' : 'bg-green-50'">
              <div class="text-sm text-gray-500">ผลต่าง</div>
              <div class="text-2xl font-bold" :class="comparison.difference >= 0 ? 'text-red-600' : 'text-green-600'">
                {{ comparison.difference >= 0 ? '+' : '' }}{{ formatNumber(comparison.difference) }}
                <span class="text-sm">({{ comparison.percentage >= 0 ? '+' : '' }}{{ comparison.percentage }}%)</span>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Monthly Chart -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">📈 มูลค่าการเบิกรายเดือน</h3>
            <div class="space-y-2">
              <div v-for="m in 12" :key="m" class="flex items-center gap-2">
                <div class="w-8 text-sm text-gray-500">{{ getMonthName(m) }}</div>
                <div class="flex-1 h-6 bg-gray-100 rounded overflow-hidden">
                  <div
                    class="h-full bg-indigo-500"
                    :style="{ width: getMonthBarWidth(m) + '%' }"
                  ></div>
                </div>
                <div class="w-24 text-right text-sm">฿{{ formatNumber(getMonthValue(m)) }}</div>
              </div>
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
  year: [String, Number],
  monthlyData: Array,
  summary: Object,
  topProducts: Array,
  comparison: Object,
})

const selectedYear = ref(parseInt(props.year))

const availableYears = computed(() => {
  const currentYear = new Date().getFullYear()
  return Array.from({ length: 5 }, (_, i) => currentYear - i)
})

const changeYear = () => {
  router.get(route('reports.stock-requisitions.yearly'), { year: selectedYear.value })
}

const maxMonthValue = computed(() => {
  return Math.max(...props.monthlyData.map(d => parseFloat(d.total_cost) || 0), 1)
})

const getMonthValue = (month) => {
  const data = props.monthlyData.find(d => d.month === month)
  return data ? parseFloat(data.total_cost) : 0
}

const getMonthBarWidth = (month) => {
  return (getMonthValue(month) / maxMonthValue.value) * 100
}

const getMonthName = (month) => {
  const months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.']
  return months[month - 1]
}

const formatNumber = (num) => {
  if (!num) return '0.00'
  return new Intl.NumberFormat('th-TH', { minimumFractionDigits: 2 }).format(num)
}
</script>
