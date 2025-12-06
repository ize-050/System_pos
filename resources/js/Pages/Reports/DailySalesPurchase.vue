<template>
  <AppLayout title="รายงานยอดขาย-ซื้อรายวัน">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        รายงานยอดขาย-ซื้อรายวัน และสินค้าคงคลัง
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Print Header (only visible when printing) -->
        <div class="print-header hidden print:block mb-6 text-center">
          <h1 class="text-2xl font-bold">รายงานยอดขาย-ซื้อรายวัน</h1>
          <p class="text-gray-600">วันที่: {{ formatDate(filters.date) }}</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6 no-print">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">วันที่</label>
              <input
                v-model="filters.date"
                type="date"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">หมวดหมู่</label>
              <select
                v-model="filters.category_id"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              >
                <option value="">ทั้งหมด</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.name }}
                </option>
              </select>
            </div>
            <div class="flex items-end">
              <button
                @click="loadReport"
                class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
              >
                ค้นหา
              </button>
            </div>
            <div class="flex items-end">
              <button
                @click="printReport"
                class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
              >
                พิมพ์รายงาน
              </button>
            </div>
          </div>
        </div>

        <!-- Sales Report -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <h3 class="text-lg font-bold mb-4 text-gray-800">รายงานยอดขาย-ซื้อรายวัน</h3>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">วันที่</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">รหัสสินค้า</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ชื่อสินค้า</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">หมวดหมู่</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">ราคาทุน</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">ราคาขาย</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">ขาย (ชิ้น)</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">มูลค่าขาย</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">ซื้อ (ชิ้น)</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">มูลค่าซื้อ</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">คงเหลือ</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">หมายเหตุ</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="item in salesData" :key="item.product_id" class="hover:bg-gray-50">
                  <td class="px-4 py-3 text-sm">{{ formatDate(filters.date) }}</td>
                  <td class="px-4 py-3 text-sm">{{ item.sku }}</td>
                  <td class="px-4 py-3 text-sm font-medium">{{ item.product_name }}</td>
                  <td class="px-4 py-3 text-sm text-center">{{ item.category_name }}</td>
                  <td class="px-4 py-3 text-sm text-right text-blue-600">
                    ฿{{ formatPrice(item.cost_price) }}
                  </td>
                  <td class="px-4 py-3 text-sm text-right text-green-600">
                    ฿{{ formatPrice(item.selling_price) }}
                  </td>
                  <td class="px-4 py-3 text-sm text-right text-red-600 font-semibold">
                    {{ item.sold_quantity || '-' }}
                  </td>
                  <td class="px-4 py-3 text-sm text-right text-red-600">
                    ฿{{ formatPrice(item.total_sales_amount) }}
                  </td>
                  <td class="px-4 py-3 text-sm text-right text-green-600 font-semibold">
                    {{ item.purchased_quantity || '-' }}
                  </td>
                  <td class="px-4 py-3 text-sm text-right text-green-600">
                    ฿{{ formatPrice(item.total_purchase_amount) }}
                  </td>
                  <td class="px-4 py-3 text-sm text-right font-semibold">
                    {{ item.current_stock }}
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-500">{{ item.notes || '-' }}</td>
                </tr>
                <tr v-if="salesData.length === 0">
                  <td colspan="12" class="px-4 py-8 text-center text-gray-500">
                    ไม่พบข้อมูล
                  </td>
                </tr>
                <tr v-if="salesData.length > 0" class="bg-gray-100 font-bold">
                  <td colspan="6" class="px-4 py-3 text-sm text-right">รวม:</td>
                  <td class="px-4 py-3 text-sm text-right text-red-600">
                    {{ totalSold }}
                  </td>
                  <td class="px-4 py-3 text-sm text-right text-red-600">
                    ฿{{ formatPrice(totalSalesAmount) }}
                  </td>
                  <td class="px-4 py-3 text-sm text-right text-green-600">
                    {{ totalPurchased }}
                  </td>
                  <td class="px-4 py-3 text-sm text-right text-green-600">
                    ฿{{ formatPrice(totalPurchaseAmount) }}
                  </td>
                  <td colspan="2"></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Inventory Summary -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-bold mb-4 text-gray-800">สรุปสินค้าคงคลัง</h3>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">วันที่</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">รหัสสินค้า</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ชื่อสินค้า</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">หมวดหมู่</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">จำนวนคงเหลือ</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">ราคาทุน/หน่วย</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">มูลค่าคงเหลือ</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">หมายเหตุ</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="item in inventoryData" :key="item.product_id" class="hover:bg-gray-50">
                  <td class="px-4 py-3 text-sm">{{ formatDate(filters.date) }}</td>
                  <td class="px-4 py-3 text-sm">{{ item.sku }}</td>
                  <td class="px-4 py-3 text-sm font-medium">{{ item.product_name }}</td>
                  <td class="px-4 py-3 text-sm text-center">{{ item.category_name }}</td>
                  <td class="px-4 py-3 text-sm text-right font-semibold">
                    {{ item.current_stock }}
                  </td>
                  <td class="px-4 py-3 text-sm text-right">
                    ฿{{ formatPrice(item.cost_price) }}
                  </td>
                  <td class="px-4 py-3 text-sm text-right font-semibold text-blue-600">
                    ฿{{ formatPrice(item.inventory_value) }}
                  </td>
                  <td class="px-4 py-3 text-sm text-center">
                    <span
                      v-if="item.current_stock <= item.reorder_point"
                      class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800"
                    >
                      ต่ำกว่าจุดสั่งซื้อ
                    </span>
                    <span v-else class="text-gray-400">-</span>
                  </td>
                </tr>
                <tr v-if="inventoryData.length === 0">
                  <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                    ไม่พบข้อมูล
                  </td>
                </tr>
                <tr v-if="inventoryData.length > 0" class="bg-gray-100 font-bold">
                  <td colspan="4" class="px-4 py-3 text-sm text-right">รวมมูลค่าคงคลัง:</td>
                  <td class="px-4 py-3 text-sm text-right">{{ totalInventoryQty }}</td>
                  <td></td>
                  <td class="px-4 py-3 text-sm text-right text-blue-600">
                    ฿{{ formatPrice(totalInventoryValue) }}
                  </td>
                  <td></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  categories: Array,
  initialDate: String,
})

const filters = ref({
  date: props.initialDate || new Date().toISOString().split('T')[0],
  category_id: '',
})

const salesData = ref([])
const inventoryData = ref([])
const loading = ref(false)

const totalSold = computed(() => {
  return salesData.value.reduce((sum, item) => sum + (item.sold_quantity || 0), 0)
})

const totalPurchased = computed(() => {
  return salesData.value.reduce((sum, item) => sum + (item.purchased_quantity || 0), 0)
})

const totalSalesAmount = computed(() => {
  return salesData.value.reduce((sum, item) => sum + (parseFloat(item.total_sales_amount) || 0), 0)
})

const totalPurchaseAmount = computed(() => {
  return salesData.value.reduce((sum, item) => sum + (parseFloat(item.total_purchase_amount) || 0), 0)
})

const totalInventoryQty = computed(() => {
  return inventoryData.value.reduce((sum, item) => sum + item.current_stock, 0)
})

const totalInventoryValue = computed(() => {
  return inventoryData.value.reduce((sum, item) => sum + item.inventory_value, 0)
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('th-TH', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const formatPrice = (price) => {
  return parseFloat(price || 0).toFixed(2)
}

const loadReport = async () => {
  loading.value = true
  try {
    const response = await fetch(`/api/reports/daily-sales-purchase?date=${filters.value.date}&category_id=${filters.value.category_id}`, {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
    })
    const data = await response.json()
    salesData.value = data.sales_data || []
    inventoryData.value = data.inventory_data || []
  } catch (error) {
    console.error('Error loading report:', error)
    alert('เกิดข้อผิดพลาดในการโหลดรายงาน')
  } finally {
    loading.value = false
  }
}

const printReport = () => {
  window.print()
}

onMounted(() => {
  loadReport()
})
</script>

<style>
@media print {
  /* Hide navigation, sidebar, header */
  nav,
  aside,
  header,
  .no-print,
  [class*="sidebar"],
  [class*="navigation"],
  button,
  select,
  input {
    display: none !important;
  }
  
  /* Reset layout */
  body {
    print-color-adjust: exact;
    -webkit-print-color-adjust: exact;
    margin: 0;
    padding: 0;
  }
  
  /* Make main content full width */
  main,
  .py-12,
  .max-w-7xl {
    max-width: 100% !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
  }
  
  /* Show tables properly */
  table {
    width: 100%;
    font-size: 10px;
    border-collapse: collapse;
  }
  
  th, td {
    border: 1px solid #ddd;
    padding: 4px 6px;
  }
  
  /* Keep colors */
  .text-red-600 { color: #dc2626 !important; }
  .text-green-600 { color: #16a34a !important; }
  .text-blue-600 { color: #2563eb !important; }
  .bg-gray-100 { background-color: #f3f4f6 !important; }
  .bg-gray-50 { background-color: #f9fafb !important; }
  
  /* Page settings */
  @page {
    size: A4 landscape;
    margin: 10mm;
  }
  
  /* Show print header */
  .print-header {
    display: block !important;
  }
  
  /* Hide no-print elements */
  .no-print {
    display: none !important;
  }
}
</style>
