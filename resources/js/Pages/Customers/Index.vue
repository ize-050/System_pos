<template>
  <AppLayout title="จัดการลูกค้า">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        จัดการลูกค้า
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
              <h1 class="text-2xl font-bold text-gray-900">จัดการลูกค้า</h1>
              <Link :href="route('customers.create')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest disabled:opacity-25 transition ease-in-out duration-150" style="background-color: #6B7B47;" onmouseover="this.style.backgroundColor='#8A9B5A'" onmouseout="this.style.backgroundColor='#6B7B47'">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                  </svg>
                  เพิ่มลูกค้าใหม่
                </Link>
            </div>

            <!-- Statistics Cards -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
              <div class="bg-blue-50 p-4 rounded-lg">
                <div class="flex items-center">
                  <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-blue-600">ลูกค้าทั้งหมด</p>
                    <p class="text-2xl font-bold text-blue-900">{{ stats.total_customers }}</p>
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
                    <p class="text-sm font-medium text-green-600">ลูกค้าที่ใช้งาน</p>
                    <p class="text-2xl font-bold text-green-900">{{ stats.active_customers }}</p>
                  </div>
                </div>
              </div>
              <div class="bg-yellow-50 p-4 rounded-lg">
                <div class="flex items-center">
                  <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-yellow-600">ยอดค้างชำระ</p>
                    <p class="text-2xl font-bold text-yellow-900">฿{{ formatPrice(stats.total_balance) }}</p>
                  </div>
                </div>
              </div>
              <div class="bg-purple-50 p-4 rounded-lg">
                <div class="flex items-center">
                  <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-purple-600">วงเงินเครดิต</p>
                    <p class="text-2xl font-bold text-purple-900">฿{{ formatPrice(stats.total_credit_limit) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Search and Filters -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <input
                  v-model="search"
                  type="text"
                  placeholder="ค้นหาลูกค้า..."
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none transition ease-in-out duration-150"
                  style="border-color: #E2E8F0;"
                  onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                  onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                />
              </div>
              <div>
                <select
                  v-model="selectedType"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none transition ease-in-out duration-150"
                  style="border-color: #E2E8F0;"
                  onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                  onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                >
                  <option value="">ประเภททั้งหมด</option>
                  <option value="individual">บุคคลธรรมดา</option>
                  <option value="company">บริษัท</option>
                  <option value="contractor">ผู้รับเหมา</option>
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

            <!-- Customers Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      รหัสลูกค้า
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      ชื่อลูกค้า
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      เบอร์โทร
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Line ID
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      ประเภท
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      ยอดค้างชำระ
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      จำนวนคำสั่งซื้อ
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
                  <tr v-for="customer in customers.data" :key="customer.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ customer.customer_code }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">{{ customer.name || customer.company_name }}</div>
                      <div v-if="customer.email" class="text-sm text-gray-500">{{ customer.email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ customer.phone }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ customer.line_id || '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                            :class="getTypeClass(customer.customer_type)">
                        {{ getTypeLabel(customer.customer_type) }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      <span :class="customer.current_balance > 0 ? 'text-red-600 font-semibold' : 'text-gray-900'">
                        ฿{{ formatPrice(customer.current_balance) }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ customer.sales_count }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                            :class="customer.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                        {{ customer.is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <div class="flex space-x-2">
                        <Link :href="route('customers.show', customer.id)" class="text-blue-600 hover:text-blue-900">
                          ดู
                        </Link>
                        <Link :href="route('customers.edit', customer.id)" class="text-indigo-600 hover:text-indigo-900">
                          แก้ไข
                        </Link>
                        <button @click="confirmDelete(customer)" class="text-red-600 hover:text-red-900">
                          ลบ
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
              <Pagination :links="customers.links" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal :show="showDeleteModal" @close="showDeleteModal = false">
      <template #title>
        ยืนยันการลบลูกค้า
      </template>
      <template #content>
        คุณแน่ใจหรือไม่ที่จะลบลูกค้า "{{ customerToDelete?.name || customerToDelete?.company_name }}"?
        การดำเนินการนี้ไม่สามารถย้อนกลับได้
      </template>
      <template #footer>
        <SecondaryButton @click="showDeleteModal = false">
          ยกเลิก
        </SecondaryButton>
        <DangerButton class="ml-3" @click="deleteCustomer" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
          ลบลูกค้า
        </DangerButton>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'

const props = defineProps({
  customers: Object,
  filters: Object,
  stats: Object,
})

const search = ref(props.filters.search || '')
const selectedType = ref(props.filters.customer_type || '')
const selectedStatus = ref(props.filters.is_active || '')
const showDeleteModal = ref(false)
const customerToDelete = ref(null)

const form = useForm({})

// Watch for filter changes and update URL
watch([search, selectedType, selectedStatus], () => {
  router.get(route('customers.index'), {
    search: search.value,
    customer_type: selectedType.value,
    is_active: selectedStatus.value,
  }, {
    preserveState: true,
    replace: true,
  })
}, { debounce: 300 })

const clearFilters = () => {
  search.value = ''
  selectedType.value = ''
  selectedStatus.value = ''
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('th-TH', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(price || 0)
}

const getTypeLabel = (type) => {
  const types = {
    individual: 'บุคคลธรรมดา',
    company: 'บริษัท',
    contractor: 'ผู้รับเหมา'
  }
  return types[type] || type
}

const getTypeClass = (type) => {
  const classes = {
    individual: 'bg-blue-100 text-blue-800',
    company: 'bg-green-100 text-green-800',
    contractor: 'bg-purple-100 text-purple-800'
  }
  return classes[type] || 'bg-gray-100 text-gray-800'
}

const confirmDelete = (customer) => {
  customerToDelete.value = customer
  showDeleteModal.value = true
}

const deleteCustomer = () => {
  form.delete(route('customers.destroy', customerToDelete.value.id), {
    onSuccess: () => {
      showDeleteModal.value = false
      customerToDelete.value = null
    }
  })
}
</script>

<script>
export default {
  components: {
    AppLayout,
    Link,
    Pagination,
    ConfirmationModal,
    SecondaryButton,
    DangerButton,
  }
}
</script>