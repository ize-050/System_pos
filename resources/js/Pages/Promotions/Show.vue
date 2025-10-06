<template>
  <AppLayout title="รายละเอียดโปรโมชั่น">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 lg:p-8">
            <div class="flex items-center justify-between mb-6">
              <h1 class="text-2xl font-bold text-gray-900">{{ promotion.name }}</h1>
              <div class="flex space-x-2">
                <Link
                  :href="route('promotions.edit', promotion.id)"
                  class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <PencilIcon class="w-4 h-4 mr-2" />
                  แก้ไข
                </Link>
                <button
                  @click="confirmDelete"
                  class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <TrashIcon class="w-4 h-4 mr-2" />
                  ลบ
                </button>
                <Link
                  :href="route('promotions.index')"
                  class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <ArrowLeftIcon class="w-4 h-4 mr-2" />
                  กลับ
                </Link>
              </div>
            </div>

            <!-- Status Badge -->
            <div class="mb-6">
              <span
                :class="[
                  'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                  promotion.is_active
                    ? 'bg-green-100 text-green-800'
                    : 'bg-red-100 text-red-800'
                ]"
              >
                <span
                  :class="[
                    'w-2 h-2 rounded-full mr-2',
                    promotion.is_active ? 'bg-green-400' : 'bg-red-400'
                  ]"
                ></span>
                {{ promotion.is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
              </span>
              <span
                v-if="isExpired"
                class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800"
              >
                หมดอายุแล้ว
              </span>
              <span
                v-else-if="isUpcoming"
                class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"
              >
                ยังไม่เริ่ม
              </span>
              <span
                v-else
                class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800"
              >
                กำลังใช้งาน
              </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
              <!-- Main Information -->
              <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-gray-50 p-6 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">ข้อมูลพื้นฐาน</h3>
                  <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <dt class="text-sm font-medium text-gray-500">ชื่อโปรโมชั่น</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ promotion.name }}</dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">ประเภท</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ promotion.type_label }}</dd>
                    </div>
                    <div v-if="promotion.description" class="md:col-span-2">
                      <dt class="text-sm font-medium text-gray-500">รายละเอียด</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ promotion.description }}</dd>
                    </div>
                  </dl>
                </div>

                <!-- Promotion Value -->
                <div class="bg-gray-50 p-6 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">ค่าโปรโมชั่น</h3>
                  <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-if="promotion.type === 'percentage'">
                      <dt class="text-sm font-medium text-gray-500">เปอร์เซ็นต์ส่วนลด</dt>
                      <dd class="mt-1 text-lg font-semibold text-green-600">{{ promotion.value }}%</dd>
                    </div>
                    <div v-if="promotion.type === 'fixed_amount'">
                      <dt class="text-sm font-medium text-gray-500">จำนวนเงินส่วนลด</dt>
                      <dd class="mt-1 text-lg font-semibold text-green-600">฿{{ formatNumber(promotion.value) }}</dd>
                    </div>
                    <div v-if="promotion.type === 'buy_x_get_y'">
                      <dt class="text-sm font-medium text-gray-500">เงื่อนไข</dt>
                      <dd class="mt-1 text-lg font-semibold text-green-600">
                        ซื้อ {{ promotion.minimum_quantity }} แถม {{ promotion.free_quantity }}
                      </dd>
                    </div>
                    <div v-if="promotion.minimum_amount">
                      <dt class="text-sm font-medium text-gray-500">ยอดขั้นต่ำ</dt>
                      <dd class="mt-1 text-sm text-gray-900">฿{{ formatNumber(promotion.minimum_amount) }}</dd>
                    </div>
                    <div v-if="promotion.maximum_discount">
                      <dt class="text-sm font-medium text-gray-500">ส่วนลดสูงสุด</dt>
                      <dd class="mt-1 text-sm text-gray-900">฿{{ formatNumber(promotion.maximum_discount) }}</dd>
                    </div>
                  </dl>
                </div>

                <!-- Date Range -->
                <div class="bg-gray-50 p-6 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">ระยะเวลาโปรโมชั่น</h3>
                  <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <dt class="text-sm font-medium text-gray-500">วันที่เริ่มต้น</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ formatDateTime(promotion.start_date) }}</dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">วันที่สิ้นสุด</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ formatDateTime(promotion.end_date) }}</dd>
                    </div>
                  </dl>
                </div>

                <!-- Applicable Products/Categories -->
                <div class="bg-gray-50 p-6 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">สินค้าที่ใช้โปรโมชั่น</h3>
                  
                  <div v-if="applicableCategories.length > 0" class="mb-4">
                    <dt class="text-sm font-medium text-gray-500 mb-2">หมวดหมู่สินค้า</dt>
                    <div class="flex flex-wrap gap-2">
                      <span
                        v-for="category in applicableCategories"
                        :key="category.id"
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"
                      >
                        {{ category.name }}
                      </span>
                    </div>
                  </div>

                  <div v-if="applicableProducts.length > 0">
                    <dt class="text-sm font-medium text-gray-500 mb-2">สินค้า</dt>
                    <div class="flex flex-wrap gap-2">
                      <span
                        v-for="product in applicableProducts"
                        :key="product.id"
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800"
                      >
                        {{ product.name }}
                      </span>
                    </div>
                  </div>

                  <div v-if="applicableCategories.length === 0 && applicableProducts.length === 0">
                    <p class="text-sm text-gray-500">ใช้ได้กับสินค้าทั้งหมด</p>
                  </div>
                </div>
              </div>

              <!-- Sidebar -->
              <div class="space-y-6">
                <!-- Usage Statistics -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">สถิติการใช้งาน</h3>
                  <dl class="space-y-4">
                    <div>
                      <dt class="text-sm font-medium text-gray-500">จำนวนครั้งที่ใช้แล้ว</dt>
                      <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ promotion.used_count }}</dd>
                    </div>
                    <div v-if="promotion.usage_limit">
                      <dt class="text-sm font-medium text-gray-500">จำกัดการใช้งาน</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ promotion.usage_limit }} ครั้ง</dd>
                      <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                        <div
                          class="bg-green-600 h-2 rounded-full"
                          :style="{ width: `${Math.min((promotion.used_count / promotion.usage_limit) * 100, 100)}%` }"
                        ></div>
                      </div>
                      <p class="mt-1 text-xs text-gray-500">
                        เหลือ {{ Math.max(promotion.usage_limit - promotion.used_count, 0) }} ครั้ง
                      </p>
                    </div>
                    <div v-else>
                      <dt class="text-sm font-medium text-gray-500">จำกัดการใช้งาน</dt>
                      <dd class="mt-1 text-sm text-gray-900">ไม่จำกัด</dd>
                    </div>
                  </dl>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">การดำเนินการ</h3>
                  <div class="space-y-3">
                    <Link
                      :href="route('promotions.edit', promotion.id)"
                      class="w-full inline-flex justify-center items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                      <PencilIcon class="w-4 h-4 mr-2" />
                      แก้ไขโปรโมชั่น
                    </Link>
                    <button
                      @click="toggleStatus"
                      :class="[
                        'w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150',
                        promotion.is_active
                          ? 'bg-red-600 hover:bg-red-700 focus:bg-red-700 active:bg-red-900'
                          : 'bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-900'
                      ]"
                    >
                      {{ promotion.is_active ? 'ปิดใช้งาน' : 'เปิดใช้งาน' }}
                    </button>
                    <button
                      @click="confirmDelete"
                      class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                      <TrashIcon class="w-4 h-4 mr-2" />
                      ลบโปรโมชั่น
                    </button>
                  </div>
                </div>

                <!-- Timestamps -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">ข้อมูลระบบ</h3>
                  <dl class="space-y-2">
                    <div>
                      <dt class="text-sm font-medium text-gray-500">สร้างเมื่อ</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ formatDateTime(promotion.created_at) }}</dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">อัปเดตล่าสุด</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ formatDateTime(promotion.updated_at) }}</dd>
                    </div>
                  </dl>
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
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ArrowLeftIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  promotion: Object,
  products: Array,
  categories: Array
})

const isExpired = computed(() => {
  return new Date(props.promotion.end_date) < new Date()
})

const isUpcoming = computed(() => {
  return new Date(props.promotion.start_date) > new Date()
})

const applicableCategories = computed(() => {
  if (!props.promotion.applicable_categories || props.promotion.applicable_categories.length === 0) {
    return []
  }
  return props.categories.filter(category => 
    props.promotion.applicable_categories.includes(category.id)
  )
})

const applicableProducts = computed(() => {
  if (!props.promotion.applicable_products || props.promotion.applicable_products.length === 0) {
    return []
  }
  return props.products.filter(product => 
    props.promotion.applicable_products.includes(product.id)
  )
})

const formatNumber = (number) => {
  return new Intl.NumberFormat('th-TH').format(number)
}

const formatDateTime = (dateString) => {
  return new Intl.DateTimeFormat('th-TH', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date(dateString))
}

const toggleStatus = () => {
  const form = useForm({
    is_active: !props.promotion.is_active
  })
  
  form.patch(route('promotions.update', props.promotion.id), {
    preserveScroll: true,
    onSuccess: () => {
      // Page will be refreshed with updated data
    }
  })
}

const confirmDelete = () => {
  if (confirm('คุณแน่ใจหรือไม่ที่จะลบโปรโมชั่นนี้? การดำเนินการนี้ไม่สามารถยกเลิกได้')) {
    router.delete(route('promotions.destroy', props.promotion.id))
  }
}
</script>