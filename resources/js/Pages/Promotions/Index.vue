<template>
  <AppLayout title="จัดการโปรโมชั่น">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center">
              <div>
                <h2 class="text-2xl font-bold text-gray-900">จัดการโปรโมชั่น</h2>
                <p class="text-gray-600 mt-1">จัดการโปรโมชั่นและส่วนลดสำหรับสินค้า</p>
              </div>
              <Link
                :href="route('promotions.create')"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                เพิ่มโปรโมชั่น
              </Link>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6">
            <form @submit.prevent="search" class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ค้นหา</label>
                <input
                  v-model="form.search"
                  type="text"
                  placeholder="ชื่อโปรโมชั่น..."
                  class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ประเภท</label>
                <select
                  v-model="form.type"
                  class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                >
                  <option value="">ทั้งหมด</option>
                  <option value="percentage">ส่วนลดเปอร์เซ็นต์</option>
                  <option value="fixed_amount">ส่วนลดจำนวนเงิน</option>
                  <option value="buy_x_get_y">ซื้อ X ได้ Y</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">สถานะ</label>
                <select
                  v-model="form.status"
                  class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                >
                  <option value="">ทั้งหมด</option>
                  <option value="1">เปิดใช้งาน</option>
                  <option value="0">ปิดใช้งาน</option>
                </select>
              </div>
              <div class="flex items-end">
                <button
                  type="submit"
                  class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                >
                  ค้นหา
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Promotions Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    โปรโมชั่น
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    ประเภท
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    ค่าส่วนลด
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    ระยะเวลา
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    สถานะ
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    การใช้งาน
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    จัดการ
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="promotion in promotions.data" :key="promotion.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div>
                      <div class="text-sm font-medium text-gray-900">{{ promotion.name }}</div>
                      <div class="text-sm text-gray-500">{{ promotion.description }}</div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                          :class="getTypeClass(promotion.type)">
                      {{ getTypeLabel(promotion.type) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <span v-if="promotion.type === 'percentage'">{{ promotion.value }}%</span>
                    <span v-else-if="promotion.type === 'fixed_amount'">{{ formatCurrency(promotion.value) }}</span>
                    <span v-else>ซื้อ {{ promotion.minimum_quantity }} ได้ {{ promotion.free_quantity }}</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div>{{ formatDate(promotion.start_date) }}</div>
                    <div class="text-gray-500">ถึง {{ formatDate(promotion.end_date) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                          :class="promotion.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                      {{ promotion.is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div v-if="promotion.usage_limit">
                      {{ promotion.used_count || 0 }} / {{ promotion.usage_limit }}
                    </div>
                    <div v-else class="text-gray-500">ไม่จำกัด</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                      <Link
                        :href="route('promotions.show', promotion.id)"
                        class="text-green-600 hover:text-green-900"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                      </Link>
                      <Link
                        :href="route('promotions.edit', promotion.id)"
                        class="text-blue-600 hover:text-blue-900"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                      </Link>
                      <button
                        @click="deletePromotion(promotion)"
                        class="text-red-600 hover:text-red-900"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
              <div class="flex-1 flex justify-between sm:hidden">
                <Link
                  v-if="promotions.prev_page_url"
                  :href="promotions.prev_page_url"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                  ก่อนหน้า
                </Link>
                <Link
                  v-if="promotions.next_page_url"
                  :href="promotions.next_page_url"
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                  ถัดไป
                </Link>
              </div>
              <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-gray-700">
                    แสดง {{ promotions.from }} ถึง {{ promotions.to }} จาก {{ promotions.total }} รายการ
                  </p>
                </div>
                <div>
                  <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <template v-for="(link, index) in promotions.links">
                      <Link
                        v-if="link.url"
                        :key="`link-${index}`"
                        :href="link.url"
                        :class="[
                          'relative inline-flex items-center px-2 py-2 border text-sm font-medium',
                          link.active
                            ? 'z-10 bg-green-50 border-green-500 text-green-600'
                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                        ]"
                      >
                        {{ link.label }}
                      </Link>
                      <span
                        v-else
                        :key="`span-${index}`"
                        :class="[
                          'relative inline-flex items-center px-2 py-2 border text-sm font-medium',
                          'bg-white border-gray-300 text-gray-300 cursor-not-allowed'
                        ]"
                      >
                        {{ link.label }}
                      </span>
                    </template>
                  </nav>
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
import { ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  promotions: Object,
  filters: Object
})

const form = reactive({
  search: props.filters.search || '',
  type: props.filters.type || '',
  status: props.filters.status || ''
})

const search = () => {
  router.get(route('promotions.index'), form, {
    preserveState: true,
    replace: true
  })
}

const deletePromotion = (promotion) => {
  if (confirm(`คุณต้องการลบโปรโมชั่น "${promotion.name}" หรือไม่?`)) {
    router.delete(route('promotions.destroy', promotion.id))
  }
}

const getTypeLabel = (type) => {
  const labels = {
    percentage: 'ส่วนลดเปอร์เซ็นต์',
    fixed_amount: 'ส่วนลดจำนวนเงิน',
    buy_x_get_y: 'ซื้อ X ได้ Y'
  }
  return labels[type] || type
}

const getTypeClass = (type) => {
  const classes = {
    percentage: 'bg-blue-100 text-blue-800',
    fixed_amount: 'bg-green-100 text-green-800',
    buy_x_get_y: 'bg-purple-100 text-purple-800'
  }
  return classes[type] || 'bg-gray-100 text-gray-800'
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('th-TH', {
    style: 'currency',
    currency: 'THB'
  }).format(amount)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('th-TH')
}
</script>