<template>
  <AppLayout title="จัดการสินค้า">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        จัดการสินค้า
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
              <h1 class="text-2xl font-bold text-gray-900">จัดการสินค้า</h1>
              <Link :href="route('products.create')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest disabled:opacity-25 transition ease-in-out duration-150" style="background-color: #6B7B47;" onmouseover="this.style.backgroundColor='#8A9B5A'" onmouseout="this.style.backgroundColor='#6B7B47'">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                  </svg>
                  เพิ่มสินค้าใหม่
                </Link>
            </div>

            <!-- Search and Filters -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <input
                  v-model="search"
                  type="text"
                  placeholder="ค้นหาสินค้า..."
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none transition ease-in-out duration-150"
                  style="border-color: #E2E8F0;"
                  onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                  onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                />
              </div>
              <div>
                <select
                  v-model="selectedCategory"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none transition ease-in-out duration-150"
                  style="border-color: #E2E8F0;"
                  onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                  onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                >
                  <option value="">หมวดหมู่ทั้งหมด</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
              </div>
              <div>
                <select
                  v-model="selectedStatus"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none transition ease-in-out duration-150"
                  style="border-color: #E2E8F0;"
                  onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                  onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                >
                  <option value="">สถานะทั้งหมด</option>
                        <option value="active">ใช้งาน</option>
                        <option value="inactive">ไม่ใช้งาน</option>
                        <option value="low_stock">สต็อกต่ำ</option>
                        <option value="out_of_stock">สินค้าหมด</option>
                </select>
              </div>
              <div>
                <button @click="clearFilters" class="w-full px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-150 ease-in-out">
                  ล้างตัวกรอง
                </button>
              </div>
            </div>

            <!-- Products Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      รูปภาพ
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      ชื่อสินค้า
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      รหัสสินค้า
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      หมวดหมู่
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      ราคาขาย
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      ราคาต้นทุน
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      สต็อก
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      จุดสั่งซื้อ
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      สถานะ
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      การดำเนินการ
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50">
                    <!-- Product Image -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex-shrink-0 h-16 w-16">
                        <img v-if="product.image_path"
                             :src="`/storage/${product.image_path}`"
                             :alt="product.name"
                             class="h-16 w-16 rounded-lg object-cover">
                        <div v-else class="h-16 w-16 rounded-lg bg-gray-200 flex items-center justify-center">
                          <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                          </svg>
                        </div>
                      </div>
                    </td>

                    <!-- Product Name -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                      <div class="text-sm text-gray-500">{{ product.description }}</div>
                    </td>

                    <!-- SKU -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ product.sku }}
                    </td>

                    <!-- Category -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ product.category?.name || '-' }}
                    </td>

                    <!-- Selling Price -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      ฿{{ Number(product.selling_price).toLocaleString() }}
                    </td>

                    <!-- Cost Price -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      ฿{{ Number(product.cost_price).toLocaleString() }}
                    </td>

                    <!-- Stock -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      <span :class="product.current_stock <= product.reorder_point ? 'text-red-600 font-semibold' : ''">
                        {{ product.current_stock }}
                      </span>
                    </td>

                    <!-- Reorder Point -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ product.reorder_point }}
                    </td>

                    <!-- Status -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="getStatusClass(product)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                        {{ getStatusText(product) }}
                      </span>
                    </td>

                    <!-- Actions -->
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div class="flex justify-end space-x-2">
                        <Link :href="route('products.show', product.id)"
                              class="text-indigo-600 hover:text-indigo-900">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                          </svg>
                        </Link>
                        <Link :href="route('products.edit', product.id)"
                              class="text-yellow-600 hover:text-yellow-900">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                          </svg>
                        </Link>
                        <button @click="deleteProduct(product.id)"
                                class="text-red-600 hover:text-red-900">
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
            <div class="mt-6" v-if="products.links.length > 3">
              <nav class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                  <Link v-if="products.prev_page_url" :href="products.prev_page_url" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Previous
                  </Link>
                  <Link v-if="products.next_page_url" :href="products.next_page_url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Next
                  </Link>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                  <div>
                    <p class="text-sm text-gray-700">
                      Showing {{ products.from }} to {{ products.to }} of {{ products.total }} results
                    </p>
                  </div>
                  <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                      <template v-for="(link, index) in products.links">
                        <Link v-if="link.url" :key="'link-' + index" :href="link.url" :class="[
                          'relative inline-flex items-center px-2 py-2 border text-sm font-medium',
                          link.active ? 'z-10 bg-green-500 border-green-500 text-green-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                        ]">
                          {{ link.label }}
                        </Link>
                        <span v-else :key="'span-' + index" :class="[
                          'relative inline-flex items-center px-2 py-2 border text-sm font-medium bg-white border-gray-300 text-gray-500'
                        ]">
                          {{ link.label }}
                        </span>
                      </template>
                    </nav>
                  </div>
                </div>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object
})

const page = usePage()

// Reactive filters
const search = ref(props.filters.search || '')
const selectedCategory = ref(props.filters.category || '')
const selectedStatus = ref(props.filters.status || '')

// Watch for filter changes and update URL
watch([search, selectedCategory, selectedStatus], () => {
    router.get(route('products.index'), {
        search: search.value,
        category: selectedCategory.value,
        status: selectedStatus.value
    }, {
        preserveState: true,
        replace: true
    })
}, { debounce: 300 })

// Clear all filters
const clearFilters = () => {
    search.value = ''
    selectedCategory.value = ''
    selectedStatus.value = ''
}

// Status helpers
const getStatusText = (product) => {
    if (!product.is_active) return 'ไม่ใช้งาน'
    if (product.current_stock <= 0) return 'สินค้าหมด'
    if (product.current_stock <= product.reorder_point) return 'สต็อกต่ำ'
    return 'ใช้งาน'
}

const getStatusClass = (product) => {
    if (!product.is_active) return 'bg-gray-100 text-gray-800'
    if (product.current_stock <= 0) return 'bg-red-100 text-red-800'
    if (product.current_stock <= product.reorder_point) return 'bg-yellow-100 text-yellow-800'
    return 'bg-green-100 text-green-800'
}

// Delete product
const deleteProduct = (productId) => {
    if (confirm('คุณแน่ใจหรือไม่ที่จะลบสินค้านี้?')) {
        router.delete(route('products.destroy', productId), {
            onSuccess: () => {
                toast.success('ลบสินค้าเรียบร้อยแล้ว', {
                    position: 'top-right',
                    autoClose: 3000,
                })
            },
            onError: () => {
                toast.error('เกิดข้อผิดพลาดในการลบสินค้า', {
                    position: 'top-right',
                    autoClose: 3000,
                })
            }
        })
    }
}

// Show flash messages
if (page.props.flash?.success) {
    toast.success(page.props.flash.success, {
        position: 'top-right',
        autoClose: 3000,
    })
}

if (page.props.flash?.error) {
    toast.error(page.props.flash.error, {
        position: 'top-right',
        autoClose: 3000,
    })
}
</script>
