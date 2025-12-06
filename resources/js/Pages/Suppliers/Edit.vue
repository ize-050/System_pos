<template>
  <AppLayout title="แก้ไขซัพพลายเออร์">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        แก้ไขซัพพลายเออร์
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
              <h1 class="text-2xl font-bold text-gray-900">แก้ไขซัพพลายเออร์</h1>
              <Link :href="route('suppliers.index')" class="text-gray-600 hover:text-gray-900">
                &larr; กลับ
              </Link>
            </div>

            <form @submit.prevent="submit">
              <div class="space-y-6">
                <!-- Name -->
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700">
                    ชื่อซัพพลายเออร์ <span class="text-red-500">*</span>
                  </label>
                  <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    required
                  />
                  <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                </div>

                <!-- Contact Person -->
                <div>
                  <label for="contact_person" class="block text-sm font-medium text-gray-700">
                    ผู้ติดต่อ
                  </label>
                  <input
                    id="contact_person"
                    v-model="form.contact_person"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  />
                  <p v-if="form.errors.contact_person" class="mt-1 text-sm text-red-600">{{ form.errors.contact_person }}</p>
                </div>

                <!-- Phone -->
                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700">
                    เบอร์โทรศัพท์
                  </label>
                  <input
                    id="phone"
                    v-model="form.phone"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  />
                  <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
                </div>

                <!-- Email -->
                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700">
                    อีเมล
                  </label>
                  <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  />
                  <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                </div>

                <!-- Tax ID -->
                <div>
                  <label for="tax_id" class="block text-sm font-medium text-gray-700">
                    เลขประจำตัวผู้เสียภาษี
                  </label>
                  <input
                    id="tax_id"
                    v-model="form.tax_id"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  />
                  <p v-if="form.errors.tax_id" class="mt-1 text-sm text-red-600">{{ form.errors.tax_id }}</p>
                </div>

                <!-- Address -->
                <div>
                  <label for="address" class="block text-sm font-medium text-gray-700">
                    ที่อยู่
                  </label>
                  <textarea
                    id="address"
                    v-model="form.address"
                    rows="3"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  ></textarea>
                  <p v-if="form.errors.address" class="mt-1 text-sm text-red-600">{{ form.errors.address }}</p>
                </div>

                <!-- Notes -->
                <div>
                  <label for="notes" class="block text-sm font-medium text-gray-700">
                    หมายเหตุ
                  </label>
                  <textarea
                    id="notes"
                    v-model="form.notes"
                    rows="3"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  ></textarea>
                  <p v-if="form.errors.notes" class="mt-1 text-sm text-red-600">{{ form.errors.notes }}</p>
                </div>

                <!-- Is Active -->
                <div class="flex items-center">
                  <input
                    id="is_active"
                    v-model="form.is_active"
                    type="checkbox"
                    class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                  />
                  <label for="is_active" class="ml-2 block text-sm text-gray-900">
                    ใช้งาน
                  </label>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-3">
                  <Link :href="route('suppliers.index')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-150 ease-in-out">
                    ยกเลิก
                  </Link>
                  <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-4 py-2 text-white rounded-md transition duration-150 ease-in-out disabled:opacity-50"
                    style="background-color: #6B7B47;"
                    onmouseover="this.style.backgroundColor='#8A9B5A'"
                    onmouseout="this.style.backgroundColor='#6B7B47'"
                  >
                    {{ form.processing ? 'กำลังบันทึก...' : 'บันทึก' }}
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  supplier: Object,
})

const form = useForm({
  name: props.supplier.name || '',
  contact_person: props.supplier.contact_person || '',
  phone: props.supplier.phone || '',
  email: props.supplier.email || '',
  tax_id: props.supplier.tax_id || '',
  address: props.supplier.address || '',
  notes: props.supplier.notes || '',
  is_active: props.supplier.is_active ?? true,
})

const submit = () => {
  form.put(route('suppliers.update', props.supplier.id))
}
</script>
