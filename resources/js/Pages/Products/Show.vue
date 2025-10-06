<template>
  <AppLayout title="รายละเอียดสินค้า">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          รายละเอียดสินค้า
        </h2>
        <div class="flex space-x-3">
          <Link :href="route('products.edit', product.id)" 
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition ease-in-out duration-150"
              style="background-color: #6B7B47; border-color: #6B7B47;"
              onmouseover="this.style.backgroundColor='#8A9B5A'"
              onmouseout="this.style.backgroundColor='#6B7B47'">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            แก้ไขสินค้า
          </Link>
          <Link :href="route('products.index')" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            กลับไปหน้าสินค้า
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
              <h1 class="text-2xl font-bold text-gray-900">รายละเอียดสินค้า</h1>
            </div>

            <!-- Product Overview Card -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
              <div class="flex items-start space-x-6">
                <!-- Product Image -->
                <div class="flex-shrink-0">
                  <div v-if="product.image_path" class="h-32 w-32 rounded-lg overflow-hidden border border-gray-300">
                    <img :src="`/storage/${product.image_path}`" :alt="product.name" class="h-full w-full object-cover">
                  </div>
                  <div v-else class="h-32 w-32 rounded-lg bg-gray-200 flex items-center justify-center border border-gray-300">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                </div>
                
                <!-- Basic Info -->
                <div class="flex-1">
                  <h2 class="text-2xl font-bold text-gray-900">{{ product.name }}</h2>
                  <p class="text-gray-600 text-lg">รหัสสินค้า: {{ product.sku }}</p>
                  <p v-if="product.description" class="text-gray-700 mt-2">{{ product.description }}</p>
                  <div class="mt-3 flex items-center space-x-4">
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full" :class="getStockStatusClass(product.current_stock, product.reorder_point)">
                      {{ getStockStatus(product.current_stock, product.reorder_point) }}
                    </span>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full" :class="product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                      {{ product.is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                    </span>
                  </div>
                </div>

                <!-- Price Info -->
                <div class="text-right">
                  <div class="text-2xl font-bold text-green-600">฿{{ formatPrice(product.selling_price) }}</div>
                  <div class="text-sm text-gray-500">ราคาขาย</div>
                  <div class="text-lg text-gray-700 mt-1">฿{{ formatPrice(product.cost_price) }}</div>
                  <div class="text-sm text-gray-500">ราคาต้นทุน</div>
                  <div class="text-sm text-green-600 mt-1">
                    กำไร: ฿{{ formatPrice(product.selling_price - product.cost_price) }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Product Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <!-- Basic Information -->
              <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">ข้อมูลพื้นฐาน</h3>
                <dl class="space-y-3">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">ชื่อสินค้า</dt>
                    <dd class="text-sm text-gray-900">{{ product.name }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">รหัสสินค้า</dt>
                    <dd class="text-sm text-gray-900 font-mono">{{ product.sku }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">หมวดหมู่</dt>
                    <dd class="text-sm text-gray-900">{{ product.category?.name || '-' }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">หน่วย</dt>
                    <dd class="text-sm text-gray-900">{{ product.unit }}</dd>
                  </div>
                  <div v-if="product.brand">
                    <dt class="text-sm font-medium text-gray-500">ยี่ห้อ</dt>
                    <dd class="text-sm text-gray-900">{{ product.brand }}</dd>
                  </div>
                  <div v-if="product.model">
                    <dt class="text-sm font-medium text-gray-500">รุ่น</dt>
                    <dd class="text-sm text-gray-900">{{ product.model }}</dd>
                  </div>
                  <div v-if="product.size">
                    <dt class="text-sm font-medium text-gray-500">ขนาด</dt>
                    <dd class="text-sm text-gray-900">{{ product.size }}</dd>
                  </div>
                  <div v-if="product.weight">
                    <dt class="text-sm font-medium text-gray-500">น้ำหนัก</dt>
                    <dd class="text-sm text-gray-900">{{ product.weight }} กรัม</dd>
                  </div>
                  <div v-if="product.warranty_period">
                    <dt class="text-sm font-medium text-gray-500">ระยะเวลาการรับประกัน</dt>
                    <dd class="text-sm text-gray-900">{{ product.warranty_period }} เดือน</dd>
                  </div>
                </dl>
              </div>

              <!-- Pricing Information -->
              <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">ข้อมูลราคา</h3>
                <dl class="space-y-3">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">ราคาต้นทุน</dt>
                    <dd class="text-sm text-gray-900 font-semibold">฿{{ formatPrice(product.cost_price) }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">ราคาขาย</dt>
                    <dd class="text-sm text-green-600 font-semibold text-lg">฿{{ formatPrice(product.selling_price) }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">กำไรต่อหน่วย</dt>
                    <dd class="text-sm text-green-600 font-semibold">
                      ฿{{ formatPrice(product.selling_price - product.cost_price) }}
                      ({{ calculateProfitMargin(product.cost_price, product.selling_price) }}%)
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">อัตรากำไร</dt>
                    <dd class="text-sm text-purple-600 font-semibold">
                      {{ calculateMarkup(product.cost_price, product.selling_price) }}%
                    </dd>
                  </div>
                </dl>
              </div>

              <!-- Stock Information -->
              <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">ข้อมูลสต็อก</h3>
                <dl class="space-y-3">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">สต็อกปัจจุบัน</dt>
                    <dd class="text-sm font-semibold" :class="getStockTextColor(product.current_stock, product.reorder_point)">
                      {{ product.current_stock }} {{ product.unit }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">จุดสั่งซื้อ</dt>
                    <dd class="text-sm text-gray-900">{{ product.reorder_point }} {{ product.unit }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">สถานะสต็อก</dt>
                    <dd class="text-sm text-gray-900">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getStockStatusClass(product.current_stock, product.reorder_point)">
                        {{ getStockStatus(product.current_stock, product.reorder_point) }}
                      </span>
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">มูลค่าสต็อก</dt>
                    <dd class="text-sm text-gray-900 font-semibold">
                      ฿{{ formatPrice(product.current_stock * product.cost_price) }}
                    </dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Additional Information -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- System Information -->
              <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">ข้อมูลระบบ</h3>
                <dl class="space-y-3">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">สถานะ</dt>
                    <dd class="text-sm text-gray-900">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                        {{ product.is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                      </span>
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">วันที่สร้าง</dt>
                    <dd class="text-sm text-gray-900">{{ formatDate(product.created_at) }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">แก้ไขล่าสุด</dt>
                    <dd class="text-sm text-gray-900">{{ formatDate(product.updated_at) }}</dd>
                  </div>
                  <div v-if="product.created_by">
                    <dt class="text-sm font-medium text-gray-500">สร้างโดย</dt>
                    <dd class="text-sm text-gray-900">{{ product.created_by.first_name }} {{ product.created_by.last_name }}</dd>
                  </div>
                </dl>
              </div>

              <!-- Notes and Specifications -->
              <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">หมายเหตุและรายละเอียด</h3>
                <div class="space-y-3">
                  <div v-if="product.notes">
                    <dt class="text-sm font-medium text-gray-500">หมายเหตุ</dt>
                    <dd class="text-sm text-gray-700 whitespace-pre-wrap">{{ product.notes }}</dd>
                  </div>
                  <div v-if="product.specifications">
                    <dt class="text-sm font-medium text-gray-500">รายละเอียดเพิ่มเติม</dt>
                    <dd class="text-sm text-gray-700 whitespace-pre-wrap">{{ product.specifications }}</dd>
                  </div>
                  <div v-if="!product.notes && !product.specifications" class="text-sm text-gray-500 italic">
                    ไม่มีหมายเหตุหรือรายละเอียดเพิ่มเติม
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-end space-x-3">
              <button @click="deleteProduct" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                ลบสินค้า
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

const props = defineProps({
  product: Object,
})

const page = usePage()

const getStockStatus = (currentStock, reorderPoint) => {
  if (currentStock <= 0) return 'สินค้าหมด'
  if (currentStock <= reorderPoint) return 'สต็อกต่ำ'
  return 'สต็อกปกติ'
}

const getStockStatusClass = (currentStock, reorderPoint) => {
  if (currentStock <= 0) return 'bg-red-100 text-red-800'
  if (currentStock <= reorderPoint) return 'bg-yellow-100 text-yellow-800'
  return 'bg-green-100 text-green-800'
}

const getStockTextColor = (currentStock, reorderPoint) => {
  if (currentStock <= 0) return 'text-red-600'
  if (currentStock <= reorderPoint) return 'text-yellow-600'
  return 'text-green-600'
}

const formatPrice = (price) => {
  return parseFloat(price).toLocaleString('th-TH', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })
}

const calculateProfitMargin = (costPrice, sellingPrice) => {
  const profit = sellingPrice - costPrice
  const margin = (profit / sellingPrice) * 100
  return margin.toFixed(2)
}

const calculateMarkup = (costPrice, sellingPrice) => {
  const markup = ((sellingPrice - costPrice) / costPrice) * 100
  return markup.toFixed(2)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('th-TH', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const deleteProduct = () => {
  if (confirm(`คุณแน่ใจหรือไม่ที่จะลบสินค้า "${props.product.name}"? การดำเนินการนี้ไม่สามารถยกเลิกได้`)) {
    router.delete(route('products.destroy', props.product.id), {
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