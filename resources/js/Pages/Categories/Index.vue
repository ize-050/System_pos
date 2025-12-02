<template>
  <AppLayout title="จัดการหมวดหมู่สินค้า">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        จัดการหมวดหมู่สินค้า
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
              <h1 class="text-2xl font-bold text-gray-900">จัดการหมวดหมู่สินค้า</h1>
              <div class="flex gap-2">
                <!-- Add Category Button -->
                <Link :href="route('categories.create')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition ease-in-out duration-150"
                    style="background-color: #6B7B47; border-color: #6B7B47;"
                    onmouseover="this.style.backgroundColor='#8A9B5A'"
                    onmouseout="this.style.backgroundColor='#6B7B47'">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  เพิ่มหมวดหมู่ใหม่
                </Link>
                
              </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="mb-6 flex flex-col sm:flex-row gap-4">
              <!-- Search -->
              <div class="flex-1">
                <input
                  v-model="form.search"
                  @input="search"
                  type="text"
                  placeholder="ค้นหาหมวดหมู่..."
                  class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none transition ease-in-out duration-150"
                  style="border-color: #E2E8F0;"
                  onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                  onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                />
              </div>

              <!-- Status Filter -->
              <div class="w-full sm:w-48">
                <select
                  v-model="form.status"
                  @change="search"
                  class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none transition ease-in-out duration-150"
                  style="border-color: #E2E8F0;"
                  onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                  onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                >
                  <option value="">สถานะทั้งหมด</option>
                  <option value="active">ใช้งาน</option>
                  <option value="inactive">ไม่ใช้งาน</option>
                </select>
              </div>

              <!-- Clear Filters -->
              <button
                @click="clearFilters"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500"
              >
                Clear
              </button>
            </div>

            <!-- Categories Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อหมวดหมู่</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">คำอธิบาย</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">จำนวนสินค้า</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">วันที่สร้าง</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">การจัดการ</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="category in (categories?.data || [])" :key="category.id" class="hover:bg-gray-50">
                    <!-- Category Info -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
                    </td>

                    <!-- Description -->
                    <td class="px-6 py-4">
                      <div class="text-sm text-gray-900 max-w-xs truncate" :title="category.description">
                        {{ category.description || 'ไม่มีคำอธิบาย' }}
                      </div>
                    </td>

                    <!-- Products Count -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                          {{ category.products_count }} สินค้า
                        </span>
                      </div>
                    </td>

                    <!-- Status -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                            :class="getStatusClass(category.is_active)">
                        {{ category.is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                      </span>
                    </td>

                    <!-- Created Date -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(category.created_at) }}
                    </td>

                    <!-- Actions -->
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div class="flex items-center space-x-2">
                        <Link :href="route('categories.show', category.id)" class="text-blue-600 hover:text-blue-900 mr-3">
                          ดู
                        </Link>
                        <Link :href="route('categories.edit', category.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                          แก้ไข
                        </Link>
                        <button @click="deleteCategory(category)" class="text-red-600 hover:text-red-900">
                          ลบ
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Empty State -->
            <div v-if="(categories?.data || []).length === 0" class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">ไม่พบหมวดหมู่สินค้า</h3>
              <p class="mt-1 text-sm text-gray-500">เริ่มต้นด้วยการสร้างหมวดหมู่สินค้าใหม่</p>
              <div class="mt-6">
                <Link :href="route('categories.create')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition ease-in-out duration-150"
                      style="background-color: #6B7B47; border-color: #6B7B47;"
                      onmouseover="this.style.backgroundColor='#8A9B5A'"
                      onmouseout="this.style.backgroundColor='#6B7B47'">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  เพิ่มหมวดหมู่
                </Link>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="(categories?.data || []).length > 0" class="mt-6">
              <Pagination
                :links="categories?.links || []"
                :from="categories?.meta?.from || 0"
                :to="categories?.meta?.to || 0"
                :total="categories?.meta?.total || 0"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import toastStore from '@/Stores/toastStore'
import { Link, router, usePage } from '@inertiajs/vue3'
import { onMounted, reactive, watch } from 'vue'
import { debounce } from 'lodash'

const props = defineProps({
  categories: {
    type: Object,
    default: () => ({}),
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
})

const page = usePage()

const form = reactive({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
})

onMounted(() => {
  if (page.props.flash?.success) {
    toastStore.success(page.props.flash.success)
  }

  if (page.props.flash?.error) {
    toastStore.error(page.props.flash.error)
  }
})

const performSearch = () => {
  router.get(route('categories.index'), { ...form }, {
    preserveState: true,
    replace: true,
  })
}

const debouncedSearch = debounce(performSearch, 300)

watch(() => form.search, () => {
  debouncedSearch()
})

watch(() => form.status, () => {
  performSearch()
})

const clearFilters = () => {
  form.search = ''
  form.status = ''
  router.get(route('categories.index'))
}

const getStatusClass = (isActive) => (isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')

const formatDate = (date) => {
  if (!date) return 'ไม่ระบุ'
  try {
    return new Date(date).toLocaleDateString('th-TH', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    })
  } catch (error) {
    return 'ไม่ระบุ'
  }
}

const search = () => {
  debouncedSearch()
}

const deleteCategory = (category) => {
  if (category.products_count > 0) {
    toastStore.error(`ไม่สามารถลบ "${category.name}" ได้ เนื่องจากมีสินค้า ${category.products_count} รายการ กรุณาย้ายหรือลบสินค้าก่อน`)
    return
  }

  if (confirm(`คุณแน่ใจหรือไม่ที่จะลบหมวดหมู่ "${category.name}"? การกระทำนี้ไม่สามารถยกเลิกได้`)) {
    router.delete(route('categories.destroy', category.id), {
      onSuccess: () => {
        toastStore.crud.deleted('หมวดหมู่')
      },
      onError: () => {
        toastStore.crud.deleteError('หมวดหมู่')
      },
    })
  }
}

defineExpose({
  form,
  clearFilters,
  getStatusClass,
  formatDate,
  search,
  deleteCategory,
})
</script>
