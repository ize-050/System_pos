<template>
  <AppLayout title="รายละเอียดลูกค้า">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        รายละเอียดลูกค้า
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Customer Info Card -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
              <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ customer.name || customer.company_name }}</h1>
                <p class="text-sm text-gray-600 mt-1">รหัสลูกค้า: {{ customer.customer_code }}</p>
              </div>
              <div class="flex space-x-3">
                <Link :href="route('customers.edit', customer.id)" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest disabled:opacity-25 transition ease-in-out duration-150" style="background-color: #6B7B47;" onmouseover="this.style.backgroundColor='#8A9B5A'" onmouseout="this.style.backgroundColor='#6B7B47'">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                  แก้ไข
                </Link>
                <Link :href="route('customers.index')" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                  </svg>
                  กลับ
                </Link>
              </div>
            </div>

            <!-- Customer Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">ข้อมูลติดต่อ</h3>
                <div>
                  <label class="block text-sm font-medium text-gray-700">ชื่อลูกค้า</label>
                  <p class="mt-1 text-sm text-gray-900">{{ customer.name || '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">เบอร์โทรศัพท์</label>
                  <p class="mt-1 text-sm text-gray-900">{{ customer.phone || '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Line ID</label>
                  <p class="mt-1 text-sm text-gray-900">{{ customer.line_id || '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">อีเมล</label>
                  <p class="mt-1 text-sm text-gray-900">{{ customer.email || '-' }}</p>
                </div>
              </div>

              <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">ข้อมูลบริษัท</h3>
                <div>
                  <label class="block text-sm font-medium text-gray-700">ชื่อบริษัท</label>
                  <p class="mt-1 text-sm text-gray-900">{{ customer.company_name || '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">ประเภทลูกค้า</label>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="{
                    'bg-blue-100 text-blue-800': customer.customer_type === 'individual',
                    'bg-green-100 text-green-800': customer.customer_type === 'company',
                    'bg-purple-100 text-purple-800': customer.customer_type === 'contractor',
                    'bg-gray-100 text-gray-800': !customer.customer_type
                  }">
                    {{ getCustomerTypeLabel(customer.customer_type) }}
                  </span>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">เลขประจำตัวผู้เสียภาษี</label>
                  <p class="mt-1 text-sm text-gray-900">{{ customer.tax_id || '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">สถานะ</label>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="{
                    'bg-green-100 text-green-800': customer.is_active,
                    'bg-red-100 text-red-800': !customer.is_active
                  }">
                    {{ customer.is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                  </span>
                </div>
              </div>

              <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">ข้อมูลเครดิต</h3>
                <div>
                  <label class="block text-sm font-medium text-gray-700">วงเงินเครดิต</label>
                  <p class="mt-1 text-sm text-gray-900">{{ formatPrice(customer.credit_limit) }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">ยอดคงเหลือปัจจุบัน</label>
                  <p class="mt-1 text-sm" :class="{
                    'text-red-600': customer.current_balance > 0,
                    'text-green-600': customer.current_balance <= 0,
                    'text-gray-900': customer.current_balance === 0
                  }">
                    {{ formatPrice(customer.current_balance) }}
                  </p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">เงื่อนไขการชำระเงิน</label>
                  <p class="mt-1 text-sm text-gray-900">{{ customer.payment_terms ? customer.payment_terms + ' วัน' : '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">วงเงินคงเหลือ</label>
                  <p class="mt-1 text-sm" :class="{
                    'text-green-600': availableCredit >= 0,
                    'text-red-600': availableCredit < 0
                  }">
                    {{ formatPrice(availableCredit) }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Address and Notes -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700">ที่อยู่</label>
                <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ customer.address || '-' }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">หมายเหตุ</label>
                <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ customer.notes || '-' }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">ยอดขายรวม</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ statistics.total_sales }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">มูลค่ารวม</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ formatPrice(statistics.total_amount) }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">ค่าเฉลี่ยต่อบิล</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ formatPrice(statistics.average_per_sale) }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">ซื้อครั้งล่าสุด</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ statistics.last_purchase || '-' }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Purchase History -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">ประวัติการซื้อ</h3>
            
            <div v-if="sales.data.length === 0" class="text-center py-8">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">ไม่มีประวัติการซื้อ</h3>
              <p class="mt-1 text-sm text-gray-500">ลูกค้ารายนี้ยังไม่มีประวัติการซื้อสินค้า</p>
            </div>

            <div v-else class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      เลขที่บิล
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      วันที่
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      รายการสินค้า
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      ยอดรวม
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      สถานะ
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      การดำเนินการ
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="sale in sales.data" :key="sale.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ sale.sale_number }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(sale.created_at) }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                      <div class="max-w-xs">
                        <div v-for="item in sale.sale_items.slice(0, 2)" :key="item.id" class="text-xs">
                          {{ item.product.name }} ({{ item.quantity }})
                        </div>
                        <div v-if="sale.sale_items.length > 2" class="text-xs text-gray-400">
                          และอีก {{ sale.sale_items.length - 2 }} รายการ
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ formatPrice(sale.total_amount) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="{
                        'bg-green-100 text-green-800': sale.status === 'completed',
                        'bg-yellow-100 text-yellow-800': sale.status === 'pending',
                        'bg-red-100 text-red-800': sale.status === 'cancelled'
                      }">
                        {{ getSaleStatusLabel(sale.status) }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <Link :href="route('sales.show', sale.id)" class="text-indigo-600 hover:text-indigo-900">
                        ดูรายละเอียด
                      </Link>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div v-if="sales.data.length > 0" class="mt-6">
              <Pagination :links="sales.links" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'
import { computed } from 'vue'

const props = defineProps({
  customer: Object,
  sales: Object,
  statistics: Object,
})

const availableCredit = computed(() => {
  return (props.customer.credit_limit || 0) - (props.customer.current_balance || 0)
})

const formatPrice = (price) => {
  if (!price) return '฿0.00'
  return new Intl.NumberFormat('th-TH', {
    style: 'currency',
    currency: 'THB'
  }).format(price)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('th-TH', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getCustomerTypeLabel = (type) => {
  const types = {
    'individual': 'บุคคลธรรมดา',
    'company': 'บริษัท',
    'contractor': 'ผู้รับเหมา'
  }
  return types[type] || '-'
}

const getSaleStatusLabel = (status) => {
  const statuses = {
    'completed': 'สำเร็จ',
    'pending': 'รอดำเนินการ',
    'cancelled': 'ยกเลิก'
  }
  return statuses[status] || status
}
</script>