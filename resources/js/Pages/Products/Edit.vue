<template>
  <AppLayout title="แก้ไขสินค้า">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        แก้ไขสินค้า
      </h2>
    </template>
    
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-bold text-gray-900">แก้ไขสินค้า</h2>
              <Link :href="route('products.index')" class="text-blue-600 hover:text-blue-800">
                ← กลับไปหน้ารายการสินค้า
              </Link>
            </div>

            <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- SKU -->
                <div>
                  <label for="sku" class="block text-sm font-medium text-gray-700">รหัสสินค้า (SKU) *</label>
                  <input
                    id="sku"
                    v-model="form.sku"
                    type="text"
                    placeholder="เช่น PROD-001"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': errors.sku }"
                    required
                  />
                  <p v-if="errors.sku" class="mt-1 text-sm text-red-600">{{ errors.sku }}</p>
                </div>

                <!-- Product Name -->
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700">ชื่อสินค้า *</label>
                  <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    placeholder="ชื่อสินค้า"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': errors.name }"
                    required
                  />
                  <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                </div>

                <!-- Barcode -->
                <div>
                  <label for="barcode" class="block text-sm font-medium text-gray-700">Barcode</label>
                  <div class="mt-1 flex rounded-md shadow-sm">
                    <input
                      id="barcode"
                      v-model="form.barcode"
                      type="text"
                      class="flex-1 block w-full px-3 py-2 border border-gray-300 rounded-l-md shadow-sm focus:outline-none"
                      style="border-color: #E2E8F0;"
                      onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                      onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                      :class="{ 'border-red-500': errors.barcode }"
                      placeholder="สแกนหรือกรอก Barcode"
                    />
                    <button
                      type="button"
                      @click="generateBarcode"
                      class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 focus:outline-none"
                    >
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                      </svg>
                      สร้างอัตโนมัติ
                    </button>
                  </div>
                  <p class="mt-1 text-xs text-gray-500">กดปุ่ม "สร้างอัตโนมัติ" เพื่อสร้าง Barcode ใหม่</p>
                  <p v-if="errors.barcode" class="mt-1 text-sm text-red-600">{{ errors.barcode }}</p>
                </div>

                <!-- Brand -->
                <div>
                  <label for="brand" class="block text-sm font-medium text-gray-700">ยี่ห้อ</label>
                  <input
                    id="brand"
                    v-model="form.brand"
                    type="text"
                    placeholder="ยี่ห้อสินค้า"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': errors.brand }"
                  />
                  <p v-if="errors.brand" class="mt-1 text-sm text-red-600">{{ errors.brand }}</p>
                </div>

                <!-- Model -->
                <div>
                  <label for="model" class="block text-sm font-medium text-gray-700">รุ่น</label>
                  <input
                    id="model"
                    v-model="form.model"
                    type="text"
                    placeholder="รุ่นสินค้า"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': errors.model }"
                  />
                  <p v-if="errors.model" class="mt-1 text-sm text-red-600">{{ errors.model }}</p>
                </div>

                <!-- Category -->
                <div>
                  <label for="category_id" class="block text-sm font-medium text-gray-700">หมวดหมู่ *</label>
                  <select
                    id="category_id"
                    v-model.number="form.category_id"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': errors.category_id }"
                    required
                  >
                    <option value="">เลือกหมวดหมู่</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                      {{ category.name }}
                    </option>
                  </select>
                  <p v-if="errors.category_id" class="mt-1 text-sm text-red-600">{{ errors.category_id }}</p>
                </div>

                <!-- Size -->
                <div>
                  <label for="size" class="block text-sm font-medium text-gray-700">ขนาด</label>
                  <input
                    id="size"
                    v-model="form.size"
                    type="text"
                    placeholder="ขนาดสินค้า"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': errors.size }"
                  />
                  <p v-if="errors.size" class="mt-1 text-sm text-red-600">{{ errors.size }}</p>
                </div>

                <!-- Unit -->
                <div>
                  <label for="unit" class="block text-sm font-medium text-gray-700">หน่วย *</label>
                  <input
                    id="unit"
                    v-model="form.unit"
                    type="text"
                    placeholder="เช่น ชิ้น, กิโลกรัม, ลิตร"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': errors.unit }"
                    required
                  />
                  <p v-if="errors.unit" class="mt-1 text-sm text-red-600">{{ errors.unit }}</p>
                </div>

                <!-- Weight -->
                <div>
                  <label for="weight" class="block text-sm font-medium text-gray-700">น้ำหนัก (กิโลกรัม)</label>
                  <input
                    id="weight"
                    v-model.number="form.weight"
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="น้ำหนักสินค้า"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': errors.weight }"
                  />
                  <p v-if="errors.weight" class="mt-1 text-sm text-red-600">{{ errors.weight }}</p>
                </div>

                <!-- Cost Price -->
                <div>
                  <label for="cost_price" class="block text-sm font-medium text-gray-700">ราคาทุน *</label>
                  <input
                    id="cost_price"
                    v-model.number="form.cost_price"
                    type="number"
                    step="0.01"
                    min="0"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': errors.cost_price }"
                    required
                  />
                  <p v-if="errors.cost_price" class="mt-1 text-sm text-red-600">{{ errors.cost_price }}</p>
                </div>

                <!-- Selling Price -->
                <div>
                  <label for="selling_price" class="block text-sm font-medium text-gray-700">ราคาขาย *</label>
                  <input
                    id="selling_price"
                    v-model.number="form.selling_price"
                    type="number"
                    step="0.01"
                    min="0"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': errors.selling_price }"
                    required
                  />
                  <p v-if="errors.selling_price" class="mt-1 text-sm text-red-600">{{ errors.selling_price }}</p>
                </div>

                <!-- Current Stock -->
                <div>
                  <label for="current_stock" class="block text-sm font-medium text-gray-700">จำนวนคงเหลือ *</label>
                  <input
                    id="current_stock"
                    v-model.number="form.current_stock"
                    type="number"
                    min="0"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': errors.current_stock }"
                    required
                  />
                  <p v-if="errors.current_stock" class="mt-1 text-sm text-red-600">{{ errors.current_stock }}</p>
                </div>

                <!-- Reorder Point -->
                <div>
                  <label for="reorder_point" class="block text-sm font-medium text-gray-700">จุดสั่งซื้อใหม่ *</label>
                  <input
                    id="reorder_point"
                    v-model.number="form.reorder_point"
                    type="number"
                    min="0"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': errors.reorder_point }"
                    required
                  />
                  <p v-if="errors.reorder_point" class="mt-1 text-sm text-red-600">{{ errors.reorder_point }}</p>
                </div>

                <!-- Warranty Period -->
                <div>
                  <label for="warranty_period" class="block text-sm font-medium text-gray-700">ระยะเวลาการรับประกัน (เดือน)</label>
                  <input
                    id="warranty_period"
                    v-model.number="form.warranty_period"
                    type="number"
                    min="0"
                    placeholder="จำนวนเดือน"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': errors.warranty_period }"
                  />
                  <p v-if="errors.warranty_period" class="mt-1 text-sm text-red-600">{{ errors.warranty_period }}</p>
                </div>
              </div>

              <!-- Description -->
              <div>
                <label for="description" class="block text-sm font-medium text-gray-700">รายละเอียดสินค้า</label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="3"
                  placeholder="รายละเอียดสินค้า"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                  style="border-color: #E2E8F0;"
                  onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                  onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                  :class="{ 'border-red-500': errors.description }"
                ></textarea>
                <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
              </div>

              <!-- Specifications -->
              <div>
                <label for="specifications" class="block text-sm font-medium text-gray-700">ข้อมูลจำเพาะ</label>
                <textarea
                  id="specifications"
                  v-model="form.specifications"
                  rows="3"
                  placeholder="ข้อมูลจำเพาะทางเทคนิค"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                  style="border-color: #E2E8F0;"
                  onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                  onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                  :class="{ 'border-red-500': errors.specifications }"
                ></textarea>
                <p v-if="errors.specifications" class="mt-1 text-sm text-red-600">{{ errors.specifications }}</p>
              </div>

              <!-- Image Section -->
              <div class="md:col-span-2">
                <label for="image" class="block text-sm font-medium text-gray-700">รูปภาพสินค้า</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors duration-200">
                  <div class="space-y-1 text-center">
                    <div v-if="imagePreview || product.image_url" class="mb-4">
                      <img 
                        :src="imagePreview || product.image_url" 
                        :alt="product.name" 
                        class="mx-auto h-32 w-32 object-cover rounded-lg"
                      >
                      <button 
                        @click="removeImage" 
                        type="button" 
                        class="mt-2 text-sm text-red-600 hover:text-red-800"
                      >
                        ลบรูปภาพ
                      </button>
                    </div>
                    <div v-else>
                      <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                    </div>
                    <div class="flex text-sm text-gray-600">
                      <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                        <span>อัปโหลดรูปภาพใหม่</span>
                        <input
                          id="image"
                          @change="handleImageUpload"
                          type="file"
                          accept="image/*"
                          class="sr-only"
                        />
                      </label>
                      <p class="pl-1">หรือลากและวาง</p>
                    </div>
                    <p class="text-xs text-gray-500">
                      รองรับไฟล์ PNG, JPG, GIF ขนาดไม่เกิน 2MB
                    </p>
                  </div>
                </div>
                <p v-if="errors.image" class="mt-1 text-sm text-red-600">{{ errors.image }}</p>
              </div>

              <!-- Notes -->
              <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">หมายเหตุ</label>
                <textarea
                  id="notes"
                  v-model="form.notes"
                  rows="3"
                  placeholder="หมายเหตุเพิ่มเติม"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                  style="border-color: #E2E8F0;"
                  onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                  onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                  :class="{ 'border-red-500': errors.notes }"
                ></textarea>
                <p v-if="errors.notes" class="mt-1 text-sm text-red-600">{{ errors.notes }}</p>
              </div>

              <!-- Status -->
              <div class="flex items-center">
                <input
                  id="is_active"
                  v-model="form.is_active"
                  type="checkbox"
                  class="h-4 w-4 border-gray-300 rounded transition ease-in-out duration-150"
                    style="color: #6B7B47; border-color: #E2E8F0;"
                    onfocus="this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.boxShadow='none'"
                />
                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                  สินค้าพร้อมขาย
                </label>
              </div>

              <!-- Submit Buttons -->
              <div class="flex justify-end space-x-3">
                <Link :href="route('products.index')" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                  ยกเลิก
                </Link>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest disabled:opacity-25 transition ease-in-out duration-150"
                    style="background-color: #6B7B47; border-color: #6B7B47;"
                    onmouseover="this.style.backgroundColor='#8A9B5A'"
                    onmouseout="this.style.backgroundColor='#6B7B47'"
                  >
                  <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ form.processing ? 'กำลังบันทึก...' : 'บันทึกการแก้ไข' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { useForm, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import toastStore from '@/Stores/toastStore'

const page = usePage()

export default {
  components: {
    AppLayout,
    Head,
    Link,
  },
  props: {
    product: Object,
    categories: Array,
    errors: Object,
  },
  setup(props) {
    const imagePreview = ref(null)

    // Computed property for errors
    const errors = computed(() => {
      return page.props.errors || {}
    })

    const parseNumber = (value, fallback = null) => {
      if (value === null || value === undefined || value === '') {
        return fallback
      }

      const parsed = Number(value)
      return Number.isNaN(parsed) ? fallback : parsed
    }

    const parseBoolean = (value) => {
      if (value === true || value === false) {
        return value
      }

      if (value === 1 || value === '1') {
        return true
      }

      if (value === 0 || value === '0') {
        return false
      }

      return Boolean(value)
    }

    const form = useForm({
      sku: props.product.sku ?? '',
      barcode: props.product.barcode ?? '',
      name: props.product.name ?? '',
      description: props.product.description ?? '',
      category_id: props.product.category_id ?? '',
      brand: props.product.brand ?? '',
      model: props.product.model ?? '',
      size: props.product.size ?? '',
      weight: parseNumber(props.product.weight, null),
      unit: props.product.unit ?? '',
      cost_price: parseNumber(
        props.product.cost_price,
        parseNumber(props.product.selling_price, 0)
      ),
      selling_price: parseNumber(
        props.product.selling_price,
        parseNumber(props.product.cost_price, 0)
      ),
      current_stock: parseNumber(props.product.current_stock, 0),
      reorder_point: parseNumber(props.product.reorder_point, 0),
      warranty_period: parseNumber(props.product.warranty_period, null),
      specifications: props.product.specifications ?? '',
      image: null,
      notes: props.product.notes ?? '',
      is_active: parseBoolean(props.product.is_active),
    })

    const handleImageUpload = (event) => {
      const file = event.target.files[0]
      if (file) {
        form.image = file
        const reader = new FileReader()
        reader.onload = (e) => {
          imagePreview.value = e.target.result
        }
        reader.readAsDataURL(file)
      }
    }

    // Set initial image preview if product has image
    if (props.product.image_url) {
      imagePreview.value = props.product.image_url
    }

    // Generate Barcode automatically
    const generateBarcode = () => {
      const timestamp = Date.now().toString().slice(-10)
      const random = Math.floor(Math.random() * 100).toString().padStart(2, '0')
      form.barcode = timestamp + random
    }

    const removeImage = () => {
      form.image = null
      imagePreview.value = null
      document.getElementById('image').value = ''
    }

    const submit = () => {
      form.transform((data) => ({
        ...data,
        _method: 'put',
      }))
      .post(route('products.update', props.product.id), {
        forceFormData: true,
        onSuccess: () => {
          toastStore.crud.updated('สินค้า')
        },
        onError: () => {
          toastStore.crud.updateError('สินค้า')
        }
      })
    }

    // Show flash messages
    if (page.props.flash?.success) {
      toastStore.success(page.props.flash.success)
    }

    if (page.props.flash?.error) {
      toastStore.error(page.props.flash.error)
    }

    return {
      form,
      errors,
      imagePreview,
      handleImageUpload,
      removeImage,
      submit,
      generateBarcode,
    }
  },
}
</script>