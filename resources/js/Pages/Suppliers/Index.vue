<template>
  <AppLayout title="จัดการซัพพลายเออร์">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        จัดการซัพพลายเออร์
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
              <h1 class="text-2xl font-bold text-gray-900">จัดการซัพพลายเออร์</h1>
              <Link :href="route('suppliers.create')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition ease-in-out duration-150" style="background-color: #6B7B47;" onmouseover="this.style.backgroundColor='#8A9B5A'" onmouseout="this.style.backgroundColor='#6B7B47'">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                เพิ่มซัพพลายเออร์
              </Link>
            </div>

            <!-- Statistics Cards -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="bg-blue-50 p-4 rounded-lg">
                <div class="flex items-center">
                  <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-blue-600">ซัพพลายเออร์ทั้งหมด</p>
                    <p class="text-2xl font-bold text-blue-900">{{ stats.total_suppliers }}</p>
                  </div>
                </div>
              </div>
              <div class="bg-green-50 p-4 rounded-lg">
                <div class="flex items-center">
                  <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-green-600">ใช้งาน</p>
                    <p class="text-2xl font-bold text-green-900">{{ stats.active_suppliers }}</p>
                  </div>
                </div>
              </div>
              <div class="bg-red-50 p-4 rounded-lg">
                <div class="flex items-center">
                  <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-red-600">ไม่ใช้งาน</p>
                    <p class="text-2xl font-bold text-red-900">{{ stats.inactive_suppliers }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Search and Filters -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <input
                  v-model="search"
                  type="text"
                  placeholder="ค้นหาซัพพลายเออร์..."
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                />
              </div>
              <div>
                <select
                  v-model="selectedStatus"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                >
                  <option value="">สถานะทั้งหมด</option>
                  <option value="1">ใช้งาน</option>
                  <option value="0">ไม่ใช้งาน</option>
                </select>
              </div>
              <div>
                <button @click="clearFilters" class="w-full px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-150 ease-in-out">
                  ล้างตัวกรอง
                </button>
              </div>
            </div>

            <!-- Suppliers Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      ชื่อซัพพลายเออร์
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      ผู้ติดต่อ
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      เบอร์โทร
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      อีเมล
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      เลขประจำตัวผู้เสียภาษี
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      สถานะ
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      การจัดการ
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="supplier in suppliers.data" :key="supplier.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">{{ supplier.name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ supplier.contact_person || '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ supplier.phone || '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ supplier.email || '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ supplier.tax_id || '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                            :class="supplier.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                        {{ supplier.is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <div class="flex space-x-2">
                        <Link :href="route('suppliers.show', supplier.id)" class="text-blue-600 hover:text-blue-900">
                          ดู
                        </Link>
                        <Link :href="route('suppliers.edit', supplier.id)" class="text-indigo-600 hover:text-indigo-900">
                          แก้ไข
                        </Link>
                        <button @click="confirmDelete(supplier)" class="text-red-600 hover:text-red-900">
                          ลบ
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="suppliers.data.length === 0">
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                      ไม่พบข้อมูลซัพพลายเออร์
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
              <Pagination :links="suppliers.links" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal 
      :show="showDeleteModal" 
      @close="showDeleteModal = false"
      @confirm="deleteSupplier"
      title="ยืนยันการปิดการใช้งาน"
      :message="`คุณแน่ใจหรือไม่ที่จะปิดการใช้งานซัพพลายเออร์ '${supplierToDelete?.name}'?`"
      confirmText="ปิดการใช้งาน"
      cancelText="ยกเลิก"
    />
  </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router, useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'

const props = defineProps({
  suppliers: Object,
  filters: Object,
  stats: Object,
})

const search = ref(props.filters.search || '')
const selectedStatus = ref(props.filters.is_active || '')
const showDeleteModal = ref(false)
const supplierToDelete = ref(null)

const form = useForm({})

// Watch for filter changes
watch([search, selectedStatus], () => {
  router.get(route('suppliers.index'), {
    search: search.value,
    is_active: selectedStatus.value,
  }, {
    preserveState: true,
    replace: true,
  })
}, { debounce: 300 })

const clearFilters = () => {
  search.value = ''
  selectedStatus.value = ''
}

const confirmDelete = (supplier) => {
  supplierToDelete.value = supplier
  showDeleteModal.value = true
}

const deleteSupplier = () => {
  form.delete(route('suppliers.destroy', supplierToDelete.value.id), {
    onSuccess: () => {
      showDeleteModal.value = false
      supplierToDelete.value = null
    }
  })
}
</script>
