<template>
  <AppLayout title="รายงานการเบิกสินค้ารายวัน">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">รายงานการเบิกสินค้ารายวัน</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">รายงานการเบิกสินค้ารายวัน</h1>
            <p class="text-gray-600">{{ formatDateThai(date) }}</p>
          </div>
          <Link :href="route('reports.stock-requisitions')" class="text-gray-600 hover:text-gray-900">
            ← กลับ
          </Link>
        </div>

        <!-- Date Picker -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
          <div class="flex items-center gap-4">
            <label class="text-sm font-medium text-gray-700">เลือกวันที่:</label>
            <input v-model="selectedDate" type="date" @change="changeDate" class="rounded-lg border-gray-300" />
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
            <div class="text-sm text-gray-500 mb-1">ร่าง / ยกเลิก</div>
            <div class="text-3xl font-bold text-gray-600">{{ summary.draft_count }} / {{ summary.cancelled_count }}</div>
          </div>
        </div>

        <!-- By Department -->
        <div v-if="byDepartment.length > 0" class="bg-white rounded-lg shadow-sm p-6 mb-6">
          <h3 class="text-lg font-semibold mb-4">📊 แยกตามแผนก/หน้างาน</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div v-for="dept in byDepartment" :key="dept.department" class="p-4 bg-gray-50 rounded-lg">
              <div class="font-medium">{{ dept.department }}</div>
              <div class="text-sm text-gray-500">{{ dept.count }} ใบเบิก</div>
              <div class="text-lg font-bold text-indigo-600">฿{{ formatNumber(dept.total_cost) }}</div>
            </div>
          </div>
        </div>

        <!-- Requisitions List -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
          <div class="p-4 border-b">
            <h3 class="text-lg font-semibold">📋 รายการใบเบิกวันนี้</h3>
          </div>
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">เลขที่</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ผู้เบิก</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">แผนก</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">จำนวน</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">มูลค่า</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">สถานะ</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="req in requisitions" :key="req.id" class="hover:bg-gray-50">
                <td class="px-6 py-4">
                  <Link :href="route('stock-requisitions.show', req.id)" class="text-indigo-600 hover:text-indigo-900 font-medium">
                    {{ req.requisition_number }}
                  </Link>
                </td>
                <td class="px-6 py-4 text-sm">{{ req.requester_name }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ req.department || '-' }}</td>
                <td class="px-6 py-4 text-sm text-right">{{ req.total_items }}</td>
                <td class="px-6 py-4 text-sm text-right font-medium">฿{{ formatNumber(req.total_cost_amount) }}</td>
                <td class="px-6 py-4 text-center">
                  <span :class="getStatusClass(req.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                    {{ getStatusLabel(req.status) }}
                  </span>
                </td>
              </tr>
              <tr v-if="requisitions.length === 0">
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                  ไม่มีข้อมูลใบเบิกในวันนี้
                </td>
              </tr>
            </tbody>
          </table>
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
  date: String,
  requisitions: Array,
  summary: Object,
  byDepartment: Array,
})

const selectedDate = ref(props.date)

const changeDate = () => {
  router.get(route('reports.stock-requisitions.daily'), { date: selectedDate.value })
}

const formatDateThai = (date) => {
  return new Date(date).toLocaleDateString('th-TH', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
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
  const classes = { draft: 'bg-yellow-100 text-yellow-800', completed: 'bg-green-100 text-green-800', cancelled: 'bg-red-100 text-red-800' }
  return classes[status] || 'bg-gray-100 text-gray-800'
}
</script>
