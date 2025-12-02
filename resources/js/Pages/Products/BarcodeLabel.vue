<template>
  <AppLayout title="พิมพ์ป้าย Barcode">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">พิมพ์ป้าย Barcode</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Settings Panel -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">⚙️ ตั้งค่าป้าย</h3>
            
            <!-- Label Size -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">ขนาดป้าย</label>
              <select v-model="labelSize" class="w-full rounded-lg border-gray-300">
                <option value="small">เล็ก (38x25mm)</option>
                <option value="medium">กลาง (50x30mm)</option>
                <option value="large">ใหญ่ (70x40mm)</option>
              </select>
            </div>

            <!-- Barcode Type -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">ประเภท</label>
              <select v-model="barcodeType" class="w-full rounded-lg border-gray-300">
                <option value="barcode">Barcode</option>
                <option value="qrcode">QR Code</option>
              </select>
            </div>

            <!-- Show Options -->
            <div class="mb-4 space-y-2">
              <label class="flex items-center">
                <input type="checkbox" v-model="showName" class="rounded border-gray-300 text-indigo-600">
                <span class="ml-2 text-sm">แสดงชื่อสินค้า</span>
              </label>
              <label class="flex items-center">
                <input type="checkbox" v-model="showPrice" class="rounded border-gray-300 text-indigo-600">
                <span class="ml-2 text-sm">แสดงราคา</span>
              </label>
              <label class="flex items-center">
                <input type="checkbox" v-model="showSku" class="rounded border-gray-300 text-indigo-600">
                <span class="ml-2 text-sm">แสดงรหัส SKU</span>
              </label>
            </div>

            <!-- Quantity -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">จำนวนป้ายต่อสินค้า</label>
              <input v-model.number="quantity" type="number" min="1" max="100" class="w-full rounded-lg border-gray-300">
            </div>

            <!-- Columns -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">จำนวนคอลัมน์</label>
              <select v-model="columns" class="w-full rounded-lg border-gray-300">
                <option :value="2">2 คอลัมน์</option>
                <option :value="3">3 คอลัมน์</option>
                <option :value="4">4 คอลัมน์</option>
              </select>
            </div>

            <button @click="printLabels" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
              🖨️ พิมพ์ป้าย
            </button>
          </div>

          <!-- Product Selection -->
          <div class="bg-white rounded-lg shadow-sm p-6 lg:col-span-2">
            <h3 class="text-lg font-semibold mb-4">📦 เลือกสินค้า</h3>
            
            <!-- Search -->
            <div class="mb-4">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="ค้นหาสินค้า (ชื่อ, SKU, Barcode)..."
                class="w-full rounded-lg border-gray-300"
                @input="searchProducts"
              >
            </div>

            <!-- Selected Products -->
            <div v-if="selectedProducts.length > 0" class="mb-4">
              <h4 class="text-sm font-medium text-gray-700 mb-2">สินค้าที่เลือก ({{ selectedProducts.length }})</h4>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="product in selectedProducts"
                  :key="product.id"
                  class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm"
                >
                  {{ product.name }}
                  <button @click="removeProduct(product.id)" class="ml-2 text-indigo-600 hover:text-indigo-800">×</button>
                </span>
              </div>
            </div>

            <!-- Product List -->
            <div class="border rounded-lg overflow-hidden max-h-96 overflow-y-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 sticky top-0">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-10">
                      <input
                        type="checkbox"
                        @change="toggleSelectAll"
                        :checked="isAllSelected"
                        class="rounded border-gray-300 text-indigo-600"
                      >
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">สินค้า</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barcode</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">ราคา</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr
                    v-for="product in filteredProducts"
                    :key="product.id"
                    class="hover:bg-gray-50 cursor-pointer"
                    @click="toggleProduct(product)"
                  >
                    <td class="px-4 py-3">
                      <input
                        type="checkbox"
                        :checked="isSelected(product.id)"
                        @click.stop="toggleProduct(product)"
                        class="rounded border-gray-300 text-indigo-600"
                      >
                    </td>
                    <td class="px-4 py-3">
                      <div class="font-medium text-gray-900">{{ product.name }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ product.sku }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ product.barcode || '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">฿{{ formatNumber(product.selling_price) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Preview -->
        <div class="mt-6 bg-white rounded-lg shadow-sm p-6">
          <h3 class="text-lg font-semibold mb-4">👁️ ตัวอย่างป้าย</h3>
          <div class="flex flex-wrap gap-4 justify-center p-4 bg-gray-100 rounded-lg" :style="{ gridTemplateColumns: `repeat(${columns}, 1fr)` }">
            <div
              v-for="(product, index) in previewProducts"
              :key="index"
              :class="labelSizeClass"
              class="bg-white border-2 border-dashed border-gray-300 rounded p-2 flex flex-col items-center justify-center"
            >
              <div v-if="showName" class="text-xs font-medium text-center truncate w-full">{{ product.name }}</div>
              <div v-if="showPrice" class="text-sm font-bold text-red-600">฿{{ formatNumber(product.selling_price) }}</div>
              <svg v-if="barcodeType === 'barcode'" :ref="el => setBarcodeRef(el, index)" class="barcode-svg"></svg>
              <canvas v-else :ref="el => setQRRef(el, index)" class="qr-canvas"></canvas>
              <div v-if="showSku" class="text-xs text-gray-500">{{ product.sku }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Print Area (Hidden) -->
    <div id="print-area" class="hidden print:block">
      <div class="print-grid" :style="{ gridTemplateColumns: `repeat(${columns}, 1fr)` }">
        <template v-for="product in selectedProducts" :key="product.id">
          <div
            v-for="n in quantity"
            :key="`${product.id}-${n}`"
            :class="labelSizeClass"
            class="label-item"
          >
            <div v-if="showName" class="label-name">{{ product.name }}</div>
            <div v-if="showPrice" class="label-price">฿{{ formatNumber(product.selling_price) }}</div>
            <svg :id="`print-barcode-${product.id}-${n}`" class="label-barcode"></svg>
            <div v-if="showSku" class="label-sku">{{ product.sku }}</div>
          </div>
        </template>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import JsBarcode from 'jsbarcode'

const props = defineProps({
  products: Array,
})

// Settings
const labelSize = ref('medium')
const barcodeType = ref('barcode')
const showName = ref(true)
const showPrice = ref(true)
const showSku = ref(false)
const quantity = ref(1)
const columns = ref(3)

// Search & Selection
const searchQuery = ref('')
const selectedProducts = ref([])
const barcodeRefs = ref({})
const qrRefs = ref({})

const filteredProducts = computed(() => {
  if (!searchQuery.value) return props.products
  const query = searchQuery.value.toLowerCase()
  return props.products.filter(p =>
    p.name.toLowerCase().includes(query) ||
    p.sku?.toLowerCase().includes(query) ||
    p.barcode?.toLowerCase().includes(query)
  )
})

const previewProducts = computed(() => {
  return selectedProducts.value.slice(0, 6)
})

const isAllSelected = computed(() => {
  return filteredProducts.value.length > 0 &&
    filteredProducts.value.every(p => selectedProducts.value.some(s => s.id === p.id))
})

const labelSizeClass = computed(() => {
  const sizes = {
    small: 'w-24 h-16',
    medium: 'w-32 h-20',
    large: 'w-44 h-28',
  }
  return sizes[labelSize.value]
})

const isSelected = (id) => selectedProducts.value.some(p => p.id === id)

const toggleProduct = (product) => {
  const index = selectedProducts.value.findIndex(p => p.id === product.id)
  if (index === -1) {
    selectedProducts.value.push(product)
  } else {
    selectedProducts.value.splice(index, 1)
  }
}

const removeProduct = (id) => {
  selectedProducts.value = selectedProducts.value.filter(p => p.id !== id)
}

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedProducts.value = selectedProducts.value.filter(
      s => !filteredProducts.value.some(f => f.id === s.id)
    )
  } else {
    filteredProducts.value.forEach(p => {
      if (!selectedProducts.value.some(s => s.id === p.id)) {
        selectedProducts.value.push(p)
      }
    })
  }
}

const setBarcodeRef = (el, index) => {
  if (el) barcodeRefs.value[index] = el
}

const setQRRef = (el, index) => {
  if (el) qrRefs.value[index] = el
}

const formatNumber = (num) => {
  if (!num) return '0.00'
  return new Intl.NumberFormat('th-TH', { minimumFractionDigits: 2 }).format(num)
}

const generateBarcodes = () => {
  nextTick(() => {
    previewProducts.value.forEach((product, index) => {
      const barcodeValue = product.barcode || product.sku || String(product.id)
      if (barcodeType.value === 'barcode' && barcodeRefs.value[index]) {
        try {
          JsBarcode(barcodeRefs.value[index], barcodeValue, {
            format: 'CODE128',
            width: 1.5,
            height: 30,
            displayValue: true,
            fontSize: 10,
            margin: 2,
          })
        } catch (e) {
          console.error('Barcode error:', e)
        }
      }
    })
  })
}

const printLabels = () => {
  if (selectedProducts.value.length === 0) {
    alert('กรุณาเลือกสินค้าก่อน')
    return
  }

  // Generate barcodes for print
  nextTick(() => {
    selectedProducts.value.forEach((product) => {
      for (let n = 1; n <= quantity.value; n++) {
        const el = document.getElementById(`print-barcode-${product.id}-${n}`)
        if (el) {
          const barcodeValue = product.barcode || product.sku || String(product.id)
          try {
            JsBarcode(el, barcodeValue, {
              format: 'CODE128',
              width: 1.5,
              height: 30,
              displayValue: true,
              fontSize: 10,
              margin: 2,
            })
          } catch (e) {
            console.error('Print barcode error:', e)
          }
        }
      }
    })

    setTimeout(() => {
      window.print()
    }, 300)
  })
}

const searchProducts = () => {
  // Debounce handled by computed
}

// Watch for changes
watch([selectedProducts, barcodeType, labelSize], generateBarcodes, { deep: true })

onMounted(() => {
  generateBarcodes()
})
</script>

<style scoped>
.barcode-svg {
  max-width: 100%;
  height: auto;
}

.qr-canvas {
  max-width: 60px;
  height: auto;
}

@media print {
  .print-grid {
    display: grid;
    gap: 4px;
    padding: 10px;
  }

  .label-item {
    border: 1px dashed #ccc;
    padding: 4px;
    text-align: center;
    page-break-inside: avoid;
  }

  .label-name {
    font-size: 8px;
    font-weight: 600;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .label-price {
    font-size: 10px;
    font-weight: bold;
    color: #dc2626;
  }

  .label-barcode {
    max-width: 100%;
    height: auto;
  }

  .label-sku {
    font-size: 7px;
    color: #666;
  }
}
</style>
