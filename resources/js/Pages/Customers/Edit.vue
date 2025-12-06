<template>
  <AppLayout title="แก้ไขข้อมูลลูกค้า">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        แก้ไขข้อมูลลูกค้า
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
              <div>
                <h1 class="text-2xl font-bold text-gray-900">แก้ไขข้อมูลลูกค้า</h1>
                <p class="text-sm text-gray-600 mt-1">รหัสลูกค้า: {{ customer.customer_code }}</p>
              </div>
              <Link :href="route('customers.index')" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                กลับไปหน้าลูกค้า
              </Link>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Name -->
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700">ชื่อลูกค้า *</label>
                  <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': form.errors.name }"
                    required
                  />
                  <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                </div>

                <!-- Phone -->
                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700">เบอร์โทรศัพท์ *</label>
                  <input
                    id="phone"
                    v-model="form.phone"
                    type="tel"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': form.errors.phone }"
                    required
                  />
                  <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
                </div>

                <!-- Line ID -->
                <div>
                  <label for="line_id" class="block text-sm font-medium text-gray-700">Line ID</label>
                  <input
                    id="line_id"
                    v-model="form.line_id"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': form.errors.line_id }"
                  />
                  <p v-if="form.errors.line_id" class="mt-1 text-sm text-red-600">{{ form.errors.line_id }}</p>
                </div>

                <!-- Email -->
                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700">อีเมล</label>
                  <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': form.errors.email }"
                  />
                  <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                </div>

                <!-- Company Name -->
                <div>
                  <label for="company_name" class="block text-sm font-medium text-gray-700">ชื่อบริษัท</label>
                  <input
                    id="company_name"
                    v-model="form.company_name"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': form.errors.company_name }"
                  />
                  <p v-if="form.errors.company_name" class="mt-1 text-sm text-red-600">{{ form.errors.company_name }}</p>
                </div>

                <!-- Customer Type -->
                <div>
                  <label for="customer_type" class="block text-sm font-medium text-gray-700">ประเภทลูกค้า *</label>
                  <select
                    id="customer_type"
                    v-model="form.customer_type"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': form.errors.customer_type }"
                    required
                  >
                    <option value="">เลือกประเภทลูกค้า</option>
                    <option value="individual">บุคคลธรรมดา</option>
                    <option value="company">บริษัท</option>
                    <option value="contractor">ผู้รับเหมา</option>
                  </select>
                  <p v-if="form.errors.customer_type" class="mt-1 text-sm text-red-600">{{ form.errors.customer_type }}</p>
                </div>

                <!-- Tax ID -->
                <div>
                  <label for="tax_id" class="block text-sm font-medium text-gray-700">เลขประจำตัวผู้เสียภาษี</label>
                  <input
                    id="tax_id"
                    v-model="form.tax_id"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': form.errors.tax_id }"
                  />
                  <p v-if="form.errors.tax_id" class="mt-1 text-sm text-red-600">{{ form.errors.tax_id }}</p>
                </div>

                <!-- Credit Limit -->
                <div>
                  <label for="credit_limit" class="block text-sm font-medium text-gray-700">วงเงินเครดิต (บาท)</label>
                  <input
                    id="credit_limit"
                    v-model="form.credit_limit"
                    type="number"
                    step="0.01"
                    min="0"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': form.errors.credit_limit }"
                  />
                  <p v-if="form.errors.credit_limit" class="mt-1 text-sm text-red-600">{{ form.errors.credit_limit }}</p>
                </div>

                <!-- Payment Terms -->
                <div>
                  <label for="payment_terms" class="block text-sm font-medium text-gray-700">เงื่อนไขการชำระเงิน (วัน)</label>
                  <input
                    id="payment_terms"
                    v-model="form.payment_terms"
                    type="number"
                    min="0"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': form.errors.payment_terms }"
                  />
                  <p v-if="form.errors.payment_terms" class="mt-1 text-sm text-red-600">{{ form.errors.payment_terms }}</p>
                </div>

                <!-- Status -->
                <div>
                  <label class="flex items-center">
                    <input
                      v-model="form.is_active"
                      type="checkbox"
                      class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    />
                    <span class="ml-2 text-sm text-gray-600">ลูกค้าใช้งาน</span>
                  </label>
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                  <label for="address" class="block text-sm font-medium text-gray-700">ที่อยู่</label>
                  <textarea
                    id="address"
                    v-model="form.address"
                    rows="3"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': form.errors.address }"
                  ></textarea>
                  <p v-if="form.errors.address" class="mt-1 text-sm text-red-600">{{ form.errors.address }}</p>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                  <label for="notes" class="block text-sm font-medium text-gray-700">หมายเหตุ</label>
                  <textarea
                    id="notes"
                    v-model="form.notes"
                    rows="3"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': form.errors.notes }"
                  ></textarea>
                  <p v-if="form.errors.notes" class="mt-1 text-sm text-red-600">{{ form.errors.notes }}</p>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="flex items-center justify-end space-x-4">
                <Link :href="route('customers.index')" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                  ยกเลิก
                </Link>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest disabled:opacity-25 transition ease-in-out duration-150"
                  style="background-color: #6B7B47;"
                  onmouseover="this.style.backgroundColor='#8A9B5A'"
                  onmouseout="this.style.backgroundColor='#6B7B47'"
                  :class="{ 'opacity-25': form.processing }"
                >
                  <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ form.processing ? 'กำลังอัปเดต...' : 'อัปเดตข้อมูล' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { useToast } from '@/Composables/useToast'

const { success, error, info } = useToast()

const props = defineProps({
  customer: Object,
})

const form = useForm({
  name: props.customer.name || '',
  phone: props.customer.phone || '',
  line_id: props.customer.line_id || '',
  email: props.customer.email || '',
  company_name: props.customer.company_name || '',
  customer_type: props.customer.customer_type || '',
  tax_id: props.customer.tax_id || '',
  credit_limit: props.customer.credit_limit || '',
  payment_terms: props.customer.payment_terms || '',
  address: props.customer.address || '',
  notes: props.customer.notes || '',
  is_active: props.customer.is_active || false,
})

const submit = () => {
  // Check if required fields are filled
  if (!form.name || !form.phone) {
    info('กรอกข้อมูลไม่ครบ', 'กรุณากรอกชื่อลูกค้าและเบอร์โทรศัพท์')
    return
  }

  form.put(route('customers.update', props.customer.id), {
    preserveScroll: false,
    onSuccess: () => {
      success('บันทึกข้อมูลสำเร็จ', 'แก้ไขข้อมูลลูกค้าเรียบร้อยแล้ว')
    },
    onError: (errors) => {
      if (Object.keys(errors).length > 0) {
        error('บันทึกข้อมูลไม่สำเร็จ', 'กรุณาตรวจสอบข้อมูลที่กรอก')
      } else {
        error('ระบบเกิดข้อผิดพลาด', 'กรุณาลองใหม่อีกครั้ง')
      }
    },
    onFinish: () => {
      form.processing = false
    }
  })
}
</script>