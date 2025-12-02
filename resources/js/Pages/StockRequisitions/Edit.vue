<template>
  <AppLayout title="แก้ไขใบเบิกสินค้า">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">แก้ไขใบเบิกสินค้า</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
          <!-- Header -->
          <div class="flex justify-between items-center mb-6">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">แก้ไขใบเบิก: {{ requisition.requisition_number }}</h1>
              <span class="text-sm text-gray-500">สถานะ: ร่าง</span>
            </div>
            <Link :href="route('stock-requisitions.show', requisition.id)" class="text-gray-600 hover:text-gray-900">
              ← กลับ
            </Link>
          </div>

          <form @submit.prevent="submit">
            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">วันที่เบิก *</label>
                <input v-model="form.requisition_date" type="date" required class="w-full rounded-lg border-gray-300" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ผู้เบิก *</label>
                <input v-model="form.requester_name" type="text" required class="w-full rounded-lg border-gray-300" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">แผนก</label>
                <input v-model="form.department" type="text" class="w-full rounded-lg border-gray-300" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">โครงการ/หน้างาน</label>
                <input v-model="form.project_name" type="text" class="w-full rounded-lg border-gray-300" />
              </div>
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">เหตุผลการเบิก</label>
                <textarea v-model="form.reason" rows="2" class="w-full rounded-lg border-gray-300"></textarea>
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
                    placeholder="ค้นหาสินค้าเพิ่มเติม..."
                    class="w-full rounded-lg border-gray-300"
                    @input="searchProducts"
                  />
                  <div v-if="searchResults.length > 0" class="absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-60 overflow-y-auto">
                    <div
                      v-for="product in searchResults"
                      :key="product.id"
                      @click="addProduct(product)"
                      class="p-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0"
                    >
                      <div class="flex justify-between">
                        <span class="font-medium">{{ product.name }}</span>
                        <span class="text-sm text-gray-600">ราคาทุน: ฿{{ formatNumber(product.cost_price) }}</span>
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
                          class="w-full text-center rounded border-gray-300"
                          @input="updateItemTotal(index)"
                        />
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
            </div>

            <!-- Notes -->
            <div class="mb-8">
              <label class="block text-sm font-medium text-gray-700 mb-1">หมายเหตุ</label>
              <textarea v-model="form.notes" rows="2" class="w-full rounded-lg border-gray-300"></textarea>
            </div>

            <!-- Actions -->
            <div class="flex justify-between pt-6 border-t">
              <button type="button" @click="deleteRequisition" class="px-6 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50">
                ลบใบเบิก
              </button>
              <div class="flex gap-4">
                <Link :href="route('stock-requisitions.show', requisition.id)" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                  ยกเลิก
                </Link>
                <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50">
                  {{ form.processing ? 'กำลังบันทึก...' : 'บันทึกการแก้ไข' }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  requisition: Object,
  products: Array,
})

const form = useForm({
  requisition_date: props.requisition.requisition_date?.split('T')[0] || '',
  requester_name: props.requisition.requester_name || '',
  department: props.requisition.department || '',
  project_name: props.requisition.project_name || '',
  reason: props.requisition.reason || '',
  notes: props.requisition.notes || '',
  items: props.requisition.items?.map(item => ({
    product_id: item.product_id,
    product_name: item.product?.name || '',
    product_sku: item.product?.sku || '',
    quantity: item.quantity,
    cost_price: parseFloat(item.cost_price),
    total_cost: parseFloat(item.total_cost),
  })) || [],
})

const productSearch = ref('')
const searchResults = ref([])

const searchProducts = () => {
  if (productSearch.value.length < 1) {
    searchResults.value = []
    return
  }
  
  const search = productSearch.value.toLowerCase()
  searchResults.value = props.products.filter(p => 
    (p.name.toLowerCase().includes(search) || p.sku.toLowerCase().includes(search)) &&
    !form.items.some(item => item.product_id === p.id)
  ).slice(0, 10)
}

const addProduct = (product) => {
  form.items.push({
    product_id: product.id,
    product_name: product.name,
    product_sku: product.sku,
    quantity: 1,
    cost_price: parseFloat(product.cost_price),
    total_cost: parseFloat(product.cost_price),
  })
  productSearch.value = ''
  searchResults.value = []
}

const removeItem = (index) => {
  form.items.splice(index, 1)
}

const updateItemTotal = (index) => {
  const item = form.items[index]
  if (item.quantity < 1) item.quantity = 1
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
  form.put(route('stock-requisitions.update', props.requisition.id))
}

const deleteRequisition = () => {
  if (!confirm('ยืนยันการลบใบเบิกนี้?')) return
  router.delete(route('stock-requisitions.destroy', props.requisition.id))
}
</script>
