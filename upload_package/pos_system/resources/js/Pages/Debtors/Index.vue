<template>
  <AppLayout title="ลูกหนี้/เจ้าหนี้">
    <Head title="ลูกหนี้/เจ้าหนี้" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <!-- Header -->
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center">
              <h2 class="text-2xl font-bold text-gray-900">ลูกหนี้/เจ้าหนี้</h2>
            </div>
          </div>

          <!-- Tabs -->
          <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
              <button
                @click="activeTab = 'debtors'"
                :class="[
                  activeTab === 'debtors'
                    ? 'border-indigo-500 text-indigo-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
                ]"
              >
                ลูกหนี้ (Debtors)
              </button>
              <button
                @click="activeTab = 'creditors'"
                :class="[
                  activeTab === 'creditors'
                    ? 'border-indigo-500 text-indigo-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
                ]"
              >
                เจ้าหนี้ (Creditors)
              </button>
            </nav>
          </div>

          <!-- Content -->
          <div class="p-6">
            <!-- Debtors Tab -->
            <div v-if="activeTab === 'debtors'">
              <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">รายการลูกหนี้</h3>
                <div class="overflow-x-auto">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          ลูกค้า
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          ประเภท
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          วงเงินเครดิต
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          ยอดค้างชำระ
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          วงเงินคงเหลือ
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          สถานะ
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          การดำเนินการ
                        </th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr v-for="debtor in debtors" :key="debtor.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <div>
                              <div class="text-sm font-medium text-gray-900">
                                {{ debtor.name }}
                              </div>
                              <div class="text-sm text-gray-500">
                                {{ debtor.phone }}
                              </div>
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          {{ getCustomerTypeText(debtor.customer_type) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          {{ formatCurrency(debtor.credit_limit) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          {{ formatCurrency(debtor.current_balance) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          {{ formatCurrency(debtor.credit_limit - debtor.current_balance) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <span :class="[
                            debtor.current_balance > debtor.credit_limit * 0.8
                              ? 'bg-red-100 text-red-800'
                              : debtor.current_balance > debtor.credit_limit * 0.5
                              ? 'bg-yellow-100 text-yellow-800'
                              : 'bg-green-100 text-green-800',
                            'inline-flex px-2 text-xs font-semibold rounded-full'
                          ]">
                            {{ getDebtorStatus(debtor) }}
                          </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                          <Link
                            :href="route('customers.show', debtor.id)"
                            class="text-indigo-600 hover:text-indigo-900 mr-3"
                          >
                            ดูรายละเอียด
                          </Link>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Creditors Tab -->
            <div v-if="activeTab === 'creditors'">
              <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">รายการเจ้าหนี้</h3>
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                  <div class="flex">
                    <div class="flex-shrink-0">
                      <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                      </svg>
                    </div>
                    <div class="ml-3">
                      <h3 class="text-sm font-medium text-yellow-800">
                        ฟีเจอร์กำลังพัฒนา
                      </h3>
                      <div class="mt-2 text-sm text-yellow-700">
                        <p>ระบบจัดการเจ้าหนี้กำลังอยู่ระหว่างการพัฒนา จะเปิดใช้งานในเร็วๆ นี้</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

export default {
  components: {
    AppLayout,
    Head,
    Link,
  },
  props: {
    customers: Array,
  },
  setup(props) {
    const activeTab = ref('debtors')

    // Filter customers who have outstanding balance (debtors)
    const debtors = computed(() => {
      return props.customers?.filter(customer => 
        customer.current_balance > 0
      ) || []
    })

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('th-TH', {
        style: 'currency',
        currency: 'THB'
      }).format(amount || 0)
    }

    const getCustomerTypeText = (type) => {
      const types = {
        'individual': 'บุคคลทั่วไป',
        'business': 'นิติบุคคล',
        'vip': 'ลูกค้า VIP'
      }
      return types[type] || type
    }

    const getDebtorStatus = (debtor) => {
      const ratio = debtor.current_balance / debtor.credit_limit
      if (ratio > 0.8) return 'เสี่ยงสูง'
      if (ratio > 0.5) return 'เสี่ยงปานกลาง'
      return 'ปกติ'
    }

    return {
      activeTab,
      debtors,
      formatCurrency,
      getCustomerTypeText,
      getDebtorStatus,
    }
  }
}
</script>