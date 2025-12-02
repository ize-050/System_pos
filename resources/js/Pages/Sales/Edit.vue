<template>
    <AppLayout title="Edit Sale">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Edit Sale #{{ sale.id.toString().padStart(6, '0') }}
                </h2>
                <div class="space-x-2">
                    <Link
                        :href="route('sales.show', sale.id)"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                    >
                        View Sale
                    </Link>
                    <Link
                        :href="route('sales.index')"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Back to Sales
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Left Column - Product Selection -->
                        <div class="lg:col-span-2">
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">เลือกสินค้า</h3>
                                    
                                    <!-- Product Search -->
                                    <div class="mb-4 relative">
                                        <div class="relative">
                                            <input
                                                ref="searchInput"
                                                v-model="productSearch"
                                                type="text"
                                                placeholder="ค้นหาสินค้าด้วยชื่อ, SKU หรือบาร์โค้ด..."
                                                class="w-full px-4 py-3 pl-10 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all"
                                                @focus="showSearchResults = true"
                                            />
                                            <svg class="absolute left-3 top-3.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                            <div v-if="isSearching" class="absolute right-3 top-3.5">
                                                <svg class="animate-spin h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <p v-if="productSearch.length > 0 && productSearch.length < 2" class="text-xs text-gray-500 mt-1 ml-1">
                                            พิมพ์อย่างน้อย 2 ตัวอักษรเพื่อค้นหา...
                                        </p>
                                    </div>

                                    <!-- Product Results -->
                                    <div v-if="showSearchResults && searchResults.length > 0" class="mb-6 max-h-80 overflow-y-auto border-2 border-green-200 rounded-lg shadow-lg bg-white">
                                        <div
                                            v-for="product in searchResults"
                                            :key="product.id"
                                            @click="addProduct(product)"
                                            class="p-4 border-b last:border-b-0 hover:bg-green-50 cursor-pointer transition-colors duration-150"
                                        >
                                            <div class="flex justify-between items-center">
                                                <div class="flex-1">
                                                    <div class="font-semibold text-gray-900">{{ product.name }}</div>
                                                    <div class="flex items-center gap-4 mt-1 text-sm text-gray-600">
                                                        <span class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                            </svg>
                                                            คงเหลือ: <strong class="ml-1">{{ product.current_stock }}</strong>
                                                        </span>
                                                        <span class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                            </svg>
                                                            ราคา: <strong class="ml-1">฿{{ formatPrice(product.selling_price) }}</strong>
                                                        </span>
                                                        <span v-if="product.sku" class="text-xs bg-gray-100 px-2 py-1 rounded">
                                                            SKU: {{ product.sku }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <button
                                                    type="button"
                                                    class="ml-4 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-150 flex items-center"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                    เพิ่ม
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- No results message -->
                                    <div v-if="showSearchResults && productSearch.length >= 2 && searchResults.length === 0 && !isSearching" class="mb-6 p-4 border-2 border-gray-200 rounded-lg bg-gray-50 text-center text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        ไม่พบสินค้าที่ค้นหา "{{ productSearch }}"
                                    </div>

                                    <!-- Selected Items -->
                                    <div v-if="form.items.length > 0">
                                        <h4 class="text-md font-medium text-gray-900 mb-3">รายการสินค้าที่เลือก</h4>
                                        <div class="space-y-3">
                                            <div
                                                v-for="(item, index) in form.items"
                                                :key="index"
                                                class="flex items-center space-x-3 p-3 border rounded-md"
                                            >
                                                <div class="flex-1">
                                                    <div class="font-medium">{{ item.product_name }}</div>
                                                    <div class="text-sm text-gray-500">ราคา: ฿{{ formatPrice(item.unit_price) }}</div>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <button
                                                        type="button"
                                                        @click="decreaseQuantity(index)"
                                                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 py-1 rounded"
                                                    >
                                                        -
                                                    </button>
                                                    <input
                                                        v-model.number="item.quantity"
                                                        type="number"
                                                        min="1"
                                                        :max="item.max_quantity"
                                                        @input="updateItemTotal(index)"
                                                        class="w-16 px-2 py-1 border border-gray-300 rounded text-center"
                                                    />
                                                    <button
                                                        type="button"
                                                        @click="increaseQuantity(index)"
                                                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 py-1 rounded"
                                                    >
                                                        +
                                                    </button>
                                                </div>
                                                <div class="text-right">
                                                    <div class="font-medium">฿{{ formatPrice(item.total_price) }}</div>
                                                </div>
                                                <button
                                                    type="button"
                                                    @click="removeItem(index)"
                                                    class="text-red-600 hover:text-red-900"
                                                >
                                                    ลบ
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-else class="text-center py-8 text-gray-500">
                                        ค้นหาและเลือกสินค้าเพื่อเพิ่มในรายการขาย
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Sale Summary & Customer Info -->
                        <div class="space-y-6">
                            <!-- ข้อมูลลูกค้า -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">ข้อมูลลูกค้า</h3>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                ชื่อลูกค้า
                                            </label>
                                            <input
                                                v-model="form.customer_name"
                                                type="text"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                placeholder="ไม่บังคับ"
                                            />
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                เบอร์โทรศัพท์
                                            </label>
                                            <input
                                                v-model="form.customer_phone"
                                                type="text"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                placeholder="ไม่บังคับ"
                                            />
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                อีเมล
                                            </label>
                                            <input
                                                v-model="form.customer_email"
                                                type="email"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                placeholder="ไม่บังคับ"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- สรุปยอดขาย -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">สรุปยอดขาย</h3>
                                    
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span>ยอดรวม:</span>
                                            <span>฿{{ formatPrice(form.subtotal) }}</span>
                                        </div>
                                        
                                        <div class="flex justify-between items-center">
                                            <label class="text-sm font-medium text-gray-700">
                                                ส่วนลด:
                                            </label>
                                            <input
                                                v-model.number="form.discount_amount"
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                @input="calculateTotals"
                                                class="w-24 px-2 py-1 border border-gray-300 rounded text-right"
                                            />
                                        </div>
                                        
                                        <!-- VAT Toggle -->
                                        <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg border border-blue-200">
                                            <div class="flex items-center space-x-2">
                                                <input
                                                    v-model="includeVAT"
                                                    type="checkbox"
                                                    @change="toggleVAT"
                                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                />
                                                <label class="text-sm font-medium text-gray-700">
                                                    ภาษีมูลค่าเพิ่ม (VAT 7%)
                                                </label>
                                            </div>
                                            <span class="text-sm font-semibold text-blue-700">
                                                ฿{{ formatPrice(form.tax_amount || 0) }}
                                            </span>
                                        </div>
                                        
                                        <div class="border-t pt-3">
                                            <div class="flex justify-between text-lg font-bold">
                                                <span>ยอดรวมสุทธิ:</span>
                                                <span class="text-green-600">฿{{ formatPrice(form.total_amount) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">หมายเหตุ</h3>
                                    
                                    <div>
                                        <textarea
                                            v-model="form.notes"
                                            rows="3"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            placeholder="หมายเหตุเพิ่มเติม (ถ้ามี)..."
                                        ></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <button
                                        type="submit"
                                        :disabled="form.items.length === 0 || form.processing"
                                        class="w-full bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-bold py-3 px-4 rounded text-lg transition ease-in-out duration-150"
                                    >
                                        {{ form.processing ? 'กำลังบันทึก...' : 'บันทึกการแก้ไข' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { debounce } from 'lodash'
import axios from 'axios'

const props = defineProps({
    sale: Object,
    errors: Object,
})

const form = useForm({
    customer_id: props.sale.customer_id || null,
    customer_name: props.sale.customer ? (props.sale.customer.name || props.sale.customer.company_name) : '',
    customer_phone: props.sale.customer?.phone || '',
    customer_email: props.sale.customer?.email || '',
    items: [],
    subtotal: props.sale.subtotal || 0,
    tax_amount: props.sale.tax_amount || 0,
    discount_amount: props.sale.discount_amount || 0,
    total_amount: props.sale.total_amount || 0,
    payment_method: props.sale.payment_method || 'cash',
    payment_received: props.sale.payment_received || 0,
    change_amount: props.sale.change_amount || 0,
    notes: props.sale.notes || '',
    promotion_id: props.sale.promotion_id || null,
})

const productSearch = ref('')
const searchResults = ref([])
const includeVAT = ref(props.sale.tax_amount > 0)
const showSearchResults = ref(false)
const isSearching = ref(false)
const searchInput = ref(null)

// Initialize form with existing sale items
onMounted(() => {
    if (props.sale.sale_items && props.sale.sale_items.length > 0) {
        form.items = props.sale.sale_items.map(item => ({
            product_id: item.product_id,
            product_name: item.product?.name || 'Unknown Product',
            quantity: item.quantity,
            unit_price: item.unit_price,
            total_price: item.total_price,
            max_quantity: (item.product?.current_stock || 0) + item.quantity // Add back current quantity to available stock
        }))
        calculateTotals()
    }
})

// Watch for product search changes
const debouncedProductSearch = debounce(async () => {
    if (productSearch.value.length >= 2) {
        isSearching.value = true
        try {
            const response = await axios.get(route('sales.search-products'), {
                params: { search: productSearch.value }
            })
            searchResults.value = response.data
        } catch (error) {
            console.error('Error searching products:', error)
            searchResults.value = []
        } finally {
            isSearching.value = false
        }
    } else {
        searchResults.value = []
        isSearching.value = false
    }
}, 300)

watch(productSearch, debouncedProductSearch)

// Product management functions
const addProduct = (product) => {
    const existingIndex = form.items.findIndex(item => item.product_id === product.id)
    
    if (existingIndex >= 0) {
        // Increase quantity if product already exists
        const item = form.items[existingIndex]
        if (item.quantity < item.max_quantity) {
            item.quantity += 1
            updateItemTotal(existingIndex)
            calculateTotals()
        } else {
            alert('ไม่สามารถเพิ่มได้ สต็อกไม่เพียงพอ')
        }
    } else {
        // Add new product
        form.items.push({
            product_id: product.id,
            product_name: product.name,
            quantity: 1,
            unit_price: parseFloat(product.selling_price),
            total_price: parseFloat(product.selling_price),
            max_quantity: product.current_stock
        })
        calculateTotals()
    }
    
    productSearch.value = ''
    searchResults.value = []
    showSearchResults.value = false
}

const removeItem = (index) => {
    form.items.splice(index, 1)
    calculateTotals()
}

const increaseQuantity = (index) => {
    const item = form.items[index]
    if (item.quantity < item.max_quantity) {
        item.quantity += 1
        updateItemTotal(index)
        calculateTotals()
    } else {
        alert('ไม่สามารถเพิ่มได้ สต็อกไม่เพียงพอ')
    }
}

const decreaseQuantity = (index) => {
    const item = form.items[index]
    if (item.quantity > 1) {
        item.quantity -= 1
        updateItemTotal(index)
        calculateTotals()
    }
}

const updateItemTotal = (index) => {
    const item = form.items[index]
    item.total_price = item.quantity * item.unit_price
}

// VAT Toggle
const toggleVAT = () => {
    if (includeVAT.value) {
        // Calculate 7% VAT
        form.tax_amount = form.subtotal * 0.07
    } else {
        form.tax_amount = 0
    }
    calculateTotals()
}

// Calculation functions
const calculateTotals = () => {
    // Calculate subtotal from all items
    form.subtotal = form.items.reduce((sum, item) => {
        const itemTotal = parseFloat(item.total_price) || 0
        return sum + itemTotal
    }, 0)
    
    // Auto-calculate VAT if enabled
    if (includeVAT.value) {
        form.tax_amount = Math.round(form.subtotal * 0.07 * 100) / 100
    }
    
    // Calculate total amount
    const subtotal = parseFloat(form.subtotal) || 0
    const tax = parseFloat(form.tax_amount) || 0
    const discount = parseFloat(form.discount_amount) || 0
    
    form.total_amount = Math.round((subtotal + tax - discount) * 100) / 100
    calculateChange()
}

const calculateChange = () => {
    form.change_amount = Math.max(0, (form.payment_received || 0) - form.total_amount)
}

// Utility functions
const formatPrice = (price) => {
    return parseFloat(price || 0).toFixed(2)
}

// Form submission
const submit = () => {
    if (form.items.length === 0) {
        alert('กรุณาเพิ่มสินค้าอย่างน้อย 1 รายการ')
        return
    }
    
    // Set payment info to match total
    form.payment_received = form.total_amount
    form.change_amount = 0
    
    form.put(route('sales.update', props.sale.id), {
        onSuccess: () => {
            // Show success message
            alert('บันทึกการแก้ไขสำเร็จ!')
            
            // Ask if user wants to print receipt
            const printReceipt = confirm('ต้องการพิมพ์ใบเสร็จหรือไม่?')
            if (printReceipt) {
                window.open(`/receipts/${props.sale.id}/print`, '_blank', 'width=800,height=600')
            }
        },
        onError: (errors) => {
            console.error('Error updating sale:', errors)
            alert('เกิดข้อผิดพลาด: ' + (errors.message || 'ไม่สามารถบันทึกได้'))
        }
    })
}
</script>