<template>
  <AppLayout title="สร้างใบเบิกสินค้า">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">สร้างใบเบิกสินค้า</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
          <!-- Header -->
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">สร้างใบเบิกสินค้าใหม่</h1>
            <Link :href="route('stock-requisitions.index')" class="text-gray-600 hover:text-gray-900">
              ← กลับ
            </Link>
          </div>

          <form @submit.prevent="submit">
            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">วันที่เบิก *</label>
                <input v-model="form.requisition_date" type="date" required class="w-full rounded-lg border-gray-300" />
                <p v-if="form.errors.requisition_date" class="text-red-500 text-sm mt-1">{{ form.errors.requisition_date }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ผู้เบิก *</label>
                <input v-model="form.requester_name" type="text" required placeholder="ชื่อผู้เบิก" class="w-full rounded-lg border-gray-300" />
                <p v-if="form.errors.requester_name" class="text-red-500 text-sm mt-1">{{ form.errors.requester_name }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">แผนก</label>
                <input v-model="form.department" type="text" placeholder="แผนก/ฝ่าย" class="w-full rounded-lg border-gray-300" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">โครงการ/หน้างาน</label>
                <input v-model="form.project_name" type="text" placeholder="ชื่อโครงการหรือหน้างาน" class="w-full rounded-lg border-gray-300" />
              </div>
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">เหตุผลการเบิก</label>
                <textarea v-model="form.reason" rows="2" placeholder="ระบุเหตุผลการเบิก" class="w-full rounded-lg border-gray-300"></textarea>
              </div>
            </div>

            <!-- Product Selection -->
            <div class="mb-8">
              <h3 class="text-lg font-semibold mb-4">รายการสินค้า</h3>
              
              <!-- Search Product -->
              <div class="mb-4">
                <div class="relative">
                  <input
                    v-model="productSearch"
                    type="text"
                    placeholder="ค้นหาสินค้า (ชื่อ, SKU)..."
                    class="w-full rounded-lg border-gray-300 pr-10"
                    @input="searchProducts"
                  />
                  <div v-if="searchResults.length > 0" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                    <div
                      v-for="product in searchResults"
                      :key="product.id"
                      @click="addProduct(product)"
                      class="p-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0"
                    >
                      <div class="flex justify-between">
                        <div>
                          <span class="font-medium">{{ product.name }}</span>
                          <span class="text-gray-500 text-sm ml-2">({{ product.sku }})</span>
                        </div>
                        <div class="text-right">
                          <div class="text-sm text-gray-600">ราคาทุน: ฿{{ formatNumber(product.cost_price) }}</div>
                          <div class="text-xs text-gray-500">คงเหลือ: {{ product.current_stock }} {{ product.unit }}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Selected Products Table -->
              <div class="border rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">สินค้า</th>
                      <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-32">จำนวน</th>
                      <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-32">ราคาทุน</th>
                      <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-32">รวม</th>
                      <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20">ลบ</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="(item, index) in form.items" :key="index">
                      <td class="px-4 py-3">
                        <div class="font-medium">{{ item.product_name }}</div>
                        <div class="text-sm text-gray-500">{{ item.product_sku }}</div>
                      </td>
                      <td class="px-4 py-3">
                        <input
                          v-model.number="item.quantity"
                          type="number"
                          min="1"
                          :max="item.max_stock"
                          class="w-full text-center rounded border-gray-300"
                          @input="updateItemTotal(index)"
                        />
                        <div class="text-xs text-gray-500 text-center">คงเหลือ: {{ item.max_stock }}</div>
                      </td>
                      <td class="px-4 py-3 text-right">
                        ฿{{ formatNumber(item.cost_price) }}
                      </td>
                      <td class="px-4 py-3 text-right font-medium">
                        ฿{{ formatNumber(item.total_cost) }}
                      </td>
                      <td class="px-4 py-3 text-center">
                        <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-900">
                          🗑️
                        </button>
                      </td>
                    </tr>
                    <tr v-if="form.items.length === 0">
                      <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                        ยังไม่มีสินค้า - ค้นหาและเพิ่มสินค้าด้านบน
                      </td>
                    </tr>
                  </tbody>
                  <tfoot class="bg-gray-50">
                    <tr>
                      <td colspan="3" class="px-4 py-3 text-right font-semibold">รวมทั้งสิ้น:</td>
                      <td class="px-4 py-3 text-right font-bold text-lg text-indigo-600">
                        ฿{{ formatNumber(totalCost) }}
                      </td>
                      <td></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
              <p v-if="form.errors.items" class="text-red-500 text-sm mt-2">{{ form.errors.items }}</p>
            </div>

            <!-- Notes -->
            <div class="mb-8">
              <label class="block text-sm font-medium text-gray-700 mb-1">หมายเหตุ</label>
              <textarea v-model="form.notes" rows="2" placeholder="หมายเหตุเพิ่มเติม" class="w-full rounded-lg border-gray-300"></textarea>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-4">
              <Link :href="route('stock-requisitions.index')" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                ยกเลิก
              </Link>
              <button type="submit" :disabled="form.processing || form.items.length === 0" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50">
                {{ form.processing ? 'กำลังบันทึก...' : 'บันทึกใบเบิก' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  products: Array,
})

const form = useForm({
  requisition_date: new Date().toISOString().split('T')[0],
  requester_name: '',
  department: '',
  project_name: '',
  reason: '',
  notes: '',
  items: [],
})

const productSearch = ref('')
const searchResults = ref([])
let searchTimeout = null

const searchProducts = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    if (productSearch.value.length < 1) {
      searchResults.value = []
      return
    }
    
    const search = productSearch.value.toLowerCase()
    searchResults.value = props.products.filter(p => 
      (p.name.toLowerCase().includes(search) || p.sku.toLowerCase().includes(search)) &&
      !form.items.some(item => item.product_id === p.id)
    ).slice(0, 10)
  }, 200)
}

const addProduct = (product) => {
  form.items.push({
    product_id: product.id,
    product_name: product.name,
    product_sku: product.sku,
    quantity: 1,
    cost_price: parseFloat(product.cost_price),
    total_cost: parseFloat(product.cost_price),
    max_stock: product.current_stock,
    unit: product.unit,
  })
  productSearch.value = ''
  searchResults.value = []
}

const removeItem = (index) => {
  form.items.splice(index, 1)
}

const updateItemTotal = (index) => {
  const item = form.items[index]
  if (item.quantity > item.max_stock) {
    item.quantity = item.max_stock
  }
  if (item.quantity < 1) {
    item.quantity = 1
  }
  item.total_cost = item.quantity * item.cost_price
}

const totalCost = computed(() => {
  return form.items.reduce((sum, item) => sum + item.total_cost, 0)
})

const formatNumber = (num) => {
  if (!num) return '0.00'
  return new Intl.NumberFormat('th-TH', { minimumFractionDigits: 2 }).format(num)
}

const submit = () => {
  form.post(route('stock-requisitions.store'))
}
</script>
