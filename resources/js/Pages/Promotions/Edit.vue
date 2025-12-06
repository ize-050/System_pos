<template>
  <AppLayout title="แก้ไขโปรโมชั่น">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 lg:p-8">
            <div class="flex items-center justify-between mb-6">
              <h1 class="text-2xl font-bold text-gray-900">แก้ไขโปรโมชั่น: {{ promotion.name }}</h1>
              <div class="flex space-x-2">
                <Link
                  :href="route('promotions.show', promotion.id)"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  ดูรายละเอียด
                </Link>
                <Link
                  :href="route('promotions.index')"
                  class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <ArrowLeftIcon class="w-4 h-4 mr-2" />
                  กลับ
                </Link>
              </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
              <!-- Basic Information -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">ข้อมูลพื้นฐาน</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">ชื่อโปรโมชั่น *</label>
                    <input
                      id="name"
                      v-model="form.name"
                      type="text"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                      :class="{ 'border-red-500': form.errors.name }"
                      required
                    />
                    <div v-if="form.errors.name" class="mt-2 text-sm text-red-600">
                      {{ form.errors.name }}
                    </div>
                  </div>

                  <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">ประเภทโปรโมชั่น *</label>
                    <select
                      id="type"
                      v-model="form.type"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                      :class="{ 'border-red-500': form.errors.type }"
                      required
                    >
                      <option value="">เลือกประเภท</option>
                      <option value="percentage">ลดเปอร์เซ็นต์</option>
                      <option value="fixed_amount">ลดจำนวนเงิน</option>
                      <option value="buy_x_get_y">ซื้อ X แถม Y</option>
                    </select>
                    <div v-if="form.errors.type" class="mt-2 text-sm text-red-600">
                      {{ form.errors.type }}
                    </div>
                  </div>

                  <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">รายละเอียด</label>
                    <textarea
                      id="description"
                      v-model="form.description"
                      rows="3"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                      :class="{ 'border-red-500': form.errors.description }"
                    ></textarea>
                    <div v-if="form.errors.description" class="mt-2 text-sm text-red-600">
                      {{ form.errors.description }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Promotion Value -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">ค่าโปรโมชั่น</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div v-if="form.type === 'percentage'">
                    <label for="value" class="block text-sm font-medium text-gray-700">เปอร์เซ็นต์ส่วนลด *</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <input
                        id="value"
                        v-model="form.value"
                        type="number"
                        step="0.01"
                        min="0"
                        max="100"
                        class="block w-full pr-12 border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                        :class="{ 'border-red-500': form.errors.value }"
                        required
                      />
                      <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">%</span>
                      </div>
                    </div>
                    <div v-if="form.errors.value" class="mt-2 text-sm text-red-600">
                      {{ form.errors.value }}
                    </div>
                  </div>

                  <div v-if="form.type === 'fixed_amount'">
                    <label for="value" class="block text-sm font-medium text-gray-700">จำนวนเงินส่วนลด *</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <input
                        id="value"
                        v-model="form.value"
                        type="number"
                        step="0.01"
                        min="0"
                        class="block w-full pr-12 border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                        :class="{ 'border-red-500': form.errors.value }"
                        required
                      />
                      <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">฿</span>
                      </div>
                    </div>
                    <div v-if="form.errors.value" class="mt-2 text-sm text-red-600">
                      {{ form.errors.value }}
                    </div>
                  </div>

                  <div v-if="form.type === 'buy_x_get_y'" class="md:col-span-2">
                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <label for="minimum_quantity" class="block text-sm font-medium text-gray-700">ซื้อขั้นต่ำ *</label>
                        <input
                          id="minimum_quantity"
                          v-model="form.minimum_quantity"
                          type="number"
                          min="1"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          :class="{ 'border-red-500': form.errors.minimum_quantity }"
                          required
                        />
                        <div v-if="form.errors.minimum_quantity" class="mt-2 text-sm text-red-600">
                          {{ form.errors.minimum_quantity }}
                        </div>
                      </div>
                      <div>
                        <label for="free_quantity" class="block text-sm font-medium text-gray-700">แถมฟรี *</label>
                        <input
                          id="free_quantity"
                          v-model="form.free_quantity"
                          type="number"
                          min="1"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          :class="{ 'border-red-500': form.errors.free_quantity }"
                          required
                        />
                        <div v-if="form.errors.free_quantity" class="mt-2 text-sm text-red-600">
                          {{ form.errors.free_quantity }}
                        </div>
                      </div>
                    </div>
                  </div>

                  <div v-if="form.type === 'percentage' || form.type === 'fixed_amount'">
                    <label for="minimum_amount" class="block text-sm font-medium text-gray-700">ยอดขั้นต่ำ</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <input
                        id="minimum_amount"
                        v-model="form.minimum_amount"
                        type="number"
                        step="0.01"
                        min="0"
                        class="block w-full pr-12 border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                        :class="{ 'border-red-500': form.errors.minimum_amount }"
                      />
                      <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">฿</span>
                      </div>
                    </div>
                    <div v-if="form.errors.minimum_amount" class="mt-2 text-sm text-red-600">
                      {{ form.errors.minimum_amount }}
                    </div>
                  </div>

                  <div v-if="form.type === 'percentage'">
                    <label for="maximum_discount" class="block text-sm font-medium text-gray-700">ส่วนลดสูงสุด</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <input
                        id="maximum_discount"
                        v-model="form.maximum_discount"
                        type="number"
                        step="0.01"
                        min="0"
                        class="block w-full pr-12 border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                        :class="{ 'border-red-500': form.errors.maximum_discount }"
                      />
                      <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">฿</span>
                      </div>
                    </div>
                    <div v-if="form.errors.maximum_discount" class="mt-2 text-sm text-red-600">
                      {{ form.errors.maximum_discount }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Date Range -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">ระยะเวลาโปรโมชั่น</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">วันที่เริ่มต้น *</label>
                    <input
                      id="start_date"
                      v-model="form.start_date"
                      type="datetime-local"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                      :class="{ 'border-red-500': form.errors.start_date }"
                      required
                    />
                    <div v-if="form.errors.start_date" class="mt-2 text-sm text-red-600">
                      {{ form.errors.start_date }}
                    </div>
                  </div>

                  <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">วันที่สิ้นสุด *</label>
                    <input
                      id="end_date"
                      v-model="form.end_date"
                      type="datetime-local"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                      :class="{ 'border-red-500': form.errors.end_date }"
                      required
                    />
                    <div v-if="form.errors.end_date" class="mt-2 text-sm text-red-600">
                      {{ form.errors.end_date }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Applicable Products/Categories -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">สินค้าที่ใช้โปรโมชั่น</h3>
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">หมวดหมู่สินค้า</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                      <label
                        v-for="category in categories"
                        :key="category.id"
                        class="flex items-center space-x-2 p-2 border rounded-md hover:bg-gray-100 cursor-pointer"
                      >
                        <input
                          v-model="form.applicable_categories"
                          :value="category.id"
                          type="checkbox"
                          class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                        />
                        <span class="text-sm">{{ category.name }}</span>
                      </label>
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">สินค้า</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 max-h-60 overflow-y-auto">
                      <label
                        v-for="product in products"
                        :key="product.id"
                        class="flex items-center space-x-2 p-2 border rounded-md hover:bg-gray-100 cursor-pointer"
                      >
                        <input
                          v-model="form.applicable_products"
                          :value="product.id"
                          type="checkbox"
                          class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                        />
                        <span class="text-sm">{{ product.name }}</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Usage Limit -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">จำกัดการใช้งาน</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div>
                    <label for="usage_limit" class="block text-sm font-medium text-gray-700">จำนวนครั้งที่ใช้ได้</label>
                    <input
                      id="usage_limit"
                      v-model="form.usage_limit"
                      type="number"
                      min="0"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                      :class="{ 'border-red-500': form.errors.usage_limit }"
                      placeholder="ไม่จำกัด"
                    />
                    <div v-if="form.errors.usage_limit" class="mt-2 text-sm text-red-600">
                      {{ form.errors.usage_limit }}
                    </div>
                    <p class="mt-1 text-sm text-gray-500">เว้นว่างหรือใส่ 0 หากไม่ต้องการจำกัด</p>
                  </div>

                  <div>
                    <label for="used_count" class="block text-sm font-medium text-gray-700">จำนวนครั้งที่ใช้แล้ว</label>
                    <input
                      id="used_count"
                      :value="promotion.used_count"
                      type="number"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100"
                      readonly
                    />
                    <p class="mt-1 text-sm text-gray-500">ข้อมูลนี้อัปเดตอัตโนมัติ</p>
                  </div>

                  <div class="flex items-center">
                    <input
                      id="is_active"
                      v-model="form.is_active"
                      type="checkbox"
                      class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                    />
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                      เปิดใช้งานโปรโมชั่น
                    </label>
                  </div>
                </div>
              </div>

              <!-- Submit Buttons -->
              <div class="flex items-center justify-end space-x-4 pt-6">
                <Link
                  :href="route('promotions.index')"
                  class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  ยกเลิก
                </Link>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                >
                  <span v-if="form.processing">กำลังอัปเดต...</span>
                  <span v-else>อัปเดตโปรโมชั่น</span>
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
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ArrowLeftIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  promotion: Object,
  products: Array,
  categories: Array
})

const form = useForm({
  name: props.promotion.name,
  description: props.promotion.description,
  type: props.promotion.type,
  value: props.promotion.value,
  minimum_quantity: props.promotion.minimum_quantity,
  free_quantity: props.promotion.free_quantity,
  minimum_amount: props.promotion.minimum_amount,
  maximum_discount: props.promotion.maximum_discount,
  start_date: props.promotion.start_date ? new Date(props.promotion.start_date).toISOString().slice(0, 16) : '',
  end_date: props.promotion.end_date ? new Date(props.promotion.end_date).toISOString().slice(0, 16) : '',
  applicable_products: props.promotion.applicable_products || [],
  applicable_categories: props.promotion.applicable_categories || [],
  usage_limit: props.promotion.usage_limit,
  is_active: props.promotion.is_active
})

const submit = () => {
  form.put(route('promotions.update', props.promotion.id), {
    preserveScroll: false,
    onSuccess: () => {
      // Redirect will be handled by the controller
    },
    onFinish: () => {
      form.processing = false
    }
  })
}
</script>