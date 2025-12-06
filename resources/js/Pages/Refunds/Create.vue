<template>
  <AppLayout title="สร้างใบคืนสินค้า">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">สร้างใบคืนสินค้า</h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow p-6">
          <!-- Step 1: Search Sale -->
          <div v-if="!selectedSale" class="space-y-6">
            <h3 class="text-lg font-medium text-gray-900">ค้นหาบิลขาย</h3>
            
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="พิมพ์เลขที่บิล เช่น INV20251205..."
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                @input="searchSales"
              />
              
              <!-- Search Results -->
              <div v-if="searchResults.length > 0" class="absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-60 overflow-auto">
                <div
                  v-for="sale in searchResults"
                  :key="sale.id"
                  class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0"
                  @click="selectSale(sale)"
                >
                  <div class="flex justify-between items-center">
                    <div>
                      <span class="font-medium text-blue-600">{{ sale.sale_number }}</span>
                      <span class="text-gray-500 ml-2">{{ sale.customer_name }}</span>
                    </div>
                    <div class="text-right">
                      <div class="font-medium">฿{{ formatNumber(sale.total_amount) }}</div>
                      <div class="text-xs text-gray-500">{{ formatDate(sale.sale_date) }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="text-center text-gray-500 py-8">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
              <p>ค้นหาบิลขายที่ต้องการคืนสินค้า</p>
            </div>
          </div>

          <!-- Step 2: Select Items to Refund -->
          <div v-else class="space-y-6">
            <!-- Sale Info -->
            <div class="bg-gray-50 p-4 rounded-lg">
              <div class="flex justify-between items-start">
                <div>
                  <h3 class="font-medium text-gray-900">บิลอ้างอิง: {{ selectedSale.sale_number }}</h3>
                  <p class="text-sm text-gray-500">ลูกค้า: {{ selectedSale.customer?.name || 'ลูกค้าทั่วไป' }}</p>
                  <p class="text-sm text-gray-500">วันที่ขาย: {{ formatDate(selectedSale.sale_date) }}</p>
                </div>
                <button
                  @click="clearSelection"
                  class="text-gray-400 hover:text-gray-600"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Items Selection -->
            <div>
              <h4 class="font-medium text-gray-900 mb-3">เลือกสินค้าที่ต้องการคืน</h4>
              <div class="space-y-3">
                <div
                  v-for="item in saleItems"
                  :key="item.id"
                  class="border rounded-lg p-4"
                  :class="isItemSelected(item.id) ? 'border-blue-500 bg-blue-50' : 'border-gray-200'"
                >
                  <div class="flex items-center gap-4">
                    <input
                      type="checkbox"
                      :checked="isItemSelected(item.id)"
                      @change="toggleItem(item)"
                      class="h-5 w-5 text-blue-600 rounded"
                    />
                    <div class="flex-1">
                      <div class="font-medium">{{ item.product_name }}</div>
                      <div class="text-sm text-gray-500">
                        ราคา: ฿{{ formatNumber(item.unit_price) }} | 
                        ซื้อ: {{ item.quantity_sold }} ชิ้น |
                        คืนแล้ว: {{ item.quantity_refunded }} ชิ้น |
                        <span class="text-green-600">คืนได้: {{ item.quantity_available }} ชิ้น</span>
                      </div>
                    </div>
                    
                    <!-- Quantity & Options (show when selected) -->
                    <div v-if="isItemSelected(item.id)" class="flex items-center gap-3">
                      <div>
                        <label class="text-xs text-gray-500">จำนวนคืน</label>
                        <input
                          type="number"
                          :value="getSelectedItem(item.id)?.quantity"
                          @input="updateItemQuantity(item.id, $event.target.value)"
                          :max="item.quantity_available"
                          min="1"
                          class="w-20 px-2 py-1 border rounded text-center"
                        />
                      </div>
                      <div>
                        <label class="text-xs text-gray-500">สภาพ</label>
                        <select
                          :value="getSelectedItem(item.id)?.condition"
                          @change="updateItemCondition(item.id, $event.target.value)"
                          class="px-2 py-1 border rounded"
                        >
                          <option value="good">สภาพดี</option>
                          <option value="damaged">เสียหาย</option>
                          <option value="defective">ชำรุด</option>
                        </select>
                      </div>
                      <div class="flex items-center gap-1">
                        <input
                          type="checkbox"
                          :checked="getSelectedItem(item.id)?.return_to_stock"
                          @change="updateItemReturnStock(item.id, $event.target.checked)"
                          class="h-4 w-4"
                        />
                        <label class="text-xs text-gray-500">คืนสต็อก</label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Refund Details -->
            <div v-if="selectedItems.length > 0" class="border-t pt-6 space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">วิธีคืนเงิน</label>
                  <select v-model="form.refund_method" class="w-full px-3 py-2 border rounded-lg">
                    <option value="cash">เงินสด</option>
                    <option value="transfer">โอนเงิน</option>
                    <option value="credit_card">บัตรเครดิต</option>
                    <option value="store_credit">เครดิตร้าน</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">เหตุผลการคืน</label>
                  <select v-model="form.reason" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">เลือกเหตุผล</option>
                    <option value="ไซส์ไม่พอดี">ไซส์ไม่พอดี</option>
                    <option value="สินค้าชำรุด">สินค้าชำรุด</option>
                    <option value="ไม่ตรงตามสั่ง">ไม่ตรงตามสั่ง</option>
                    <option value="เปลี่ยนใจ">เปลี่ยนใจ</option>
                    <option value="อื่นๆ">อื่นๆ</option>
                  </select>
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">หมายเหตุเพิ่มเติม</label>
                <textarea
                  v-model="form.notes"
                  rows="2"
                  class="w-full px-3 py-2 border rounded-lg"
                  placeholder="รายละเอียดเพิ่มเติม..."
                ></textarea>
              </div>

              <!-- Summary -->
              <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex justify-between text-sm mb-2">
                  <span>จำนวนรายการ:</span>
                  <span>{{ selectedItems.length }} รายการ</span>
                </div>
                <div class="flex justify-between text-sm mb-2">
                  <span>ยอดรวม:</span>
                  <span>฿{{ formatNumber(calculateSubtotal) }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold border-t pt-2 mt-2">
                  <span>ยอดคืนทั้งหมด:</span>
                  <span class="text-red-600">฿{{ formatNumber(calculateSubtotal) }}</span>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex justify-end gap-3">
                <Link
                  :href="route('refunds.index')"
                  class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                >
                  ยกเลิก
                </Link>
                <button
                  @click="submitRefund"
                  :disabled="processing"
                  class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                >
                  {{ processing ? 'กำลังบันทึก...' : 'สร้างใบคืนสินค้า' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  sale: Object,
  saleItems: Array,
})

const searchQuery = ref('')
const searchResults = ref([])
const selectedSale = ref(props.sale || null)
const saleItems = ref(props.saleItems || [])
const selectedItems = ref([])
const processing = ref(false)

const form = reactive({
  refund_method: 'cash',
  reason: '',
  notes: '',
})

// Search sales
let searchTimeout = null
const searchSales = () => {
  clearTimeout(searchTimeout)
  if (searchQuery.value.length < 2) {
    searchResults.value = []
    return
  }
  
  searchTimeout = setTimeout(async () => {
    try {
      const response = await fetch(`/refunds/search-sale?q=${encodeURIComponent(searchQuery.value)}`)
      searchResults.value = await response.json()
    } catch (error) {
      console.error('Search error:', error)
    }
  }, 300)
}

const selectSale = (sale) => {
  router.get(route('refunds.create'), { sale_id: sale.id }, {
    preserveState: false,
  })
}

const clearSelection = () => {
  selectedSale.value = null
  saleItems.value = []
  selectedItems.value = []
  searchQuery.value = ''
  searchResults.value = []
}

const isItemSelected = (itemId) => {
  return selectedItems.value.some(i => i.sale_item_id === itemId)
}

const getSelectedItem = (itemId) => {
  return selectedItems.value.find(i => i.sale_item_id === itemId)
}

const toggleItem = (item) => {
  const index = selectedItems.value.findIndex(i => i.sale_item_id === item.id)
  if (index >= 0) {
    selectedItems.value.splice(index, 1)
  } else {
    selectedItems.value.push({
      sale_item_id: item.id,
      product_id: item.product_id,
      product_name: item.product_name,
      quantity: 1,
      unit_price: item.unit_price,
      max_quantity: item.quantity_available,
      condition: 'good',
      return_to_stock: true,
    })
  }
}

const updateItemQuantity = (itemId, value) => {
  const item = getSelectedItem(itemId)
  if (item) {
    const qty = parseInt(value) || 1
    item.quantity = Math.min(Math.max(1, qty), item.max_quantity)
  }
}

const updateItemCondition = (itemId, value) => {
  const item = getSelectedItem(itemId)
  if (item) {
    item.condition = value
    // Auto disable return to stock if damaged/defective
    if (value !== 'good') {
      item.return_to_stock = false
    }
  }
}

const updateItemReturnStock = (itemId, value) => {
  const item = getSelectedItem(itemId)
  if (item) {
    item.return_to_stock = value
  }
}

const calculateSubtotal = computed(() => {
  return selectedItems.value.reduce((sum, item) => {
    return sum + (item.unit_price * item.quantity)
  }, 0)
})

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

const submitRefund = () => {
  if (selectedItems.value.length === 0) {
    alert('กรุณาเลือกสินค้าที่ต้องการคืน')
    return
  }

  processing.value = true

  router.post(route('refunds.store'), {
    sale_id: selectedSale.value.id,
    items: selectedItems.value,
    refund_method: form.refund_method,
    reason: form.reason,
    notes: form.notes,
  }, {
    onFinish: () => {
      processing.value = false
    },
  })
}

onMounted(() => {
  if (props.sale) {
    selectedSale.value = props.sale
    saleItems.value = props.saleItems || []
  }
})
</script>
