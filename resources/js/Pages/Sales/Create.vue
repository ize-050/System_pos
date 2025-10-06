<template>
    <AppLayout title="New Sale">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    New Sale
                </h2>
                <Link
                    :href="route('sales.index')"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                >
                    Back to Sales
                </Link>
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
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Product Selection</h3>
                                    
                                    <!-- Product Search -->
                                    <div class="mb-4">
                                        <input
                                            v-model="productSearch"
                                            type="text"
                                            placeholder="Search products by name or barcode..."
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none transition ease-in-out duration-150"
                                            style="border-color: #E2E8F0;"
                                            onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                                            onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                                        />
                                    </div>

                                    <!-- Product Results -->
                                    <div v-if="searchResults.length > 0" class="mb-6 max-h-60 overflow-y-auto border rounded-md">
                                        <div
                                            v-for="product in searchResults"
                                            :key="product.id"
                                            @click="addProduct(product)"
                                            class="p-3 border-b hover:bg-gray-50 cursor-pointer flex justify-between items-center"
                                        >
                                            <div>
                                                <div class="font-medium">{{ product.name }}</div>
                                                <div class="text-sm text-gray-500">
                                                    Stock: {{ product.stock_quantity }} | Price: ${{ formatPrice(product.selling_price) }}
                                                </div>
                                            </div>
                                            <button
                                                type="button"
                                                class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm"
                                            >
                                                Add
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Selected Items -->
                                    <div v-if="form.items.length > 0">
                                        <h4 class="text-md font-medium text-gray-900 mb-3">Selected Items</h4>
                                        <div class="space-y-3">
                                            <div
                                                v-for="(item, index) in form.items"
                                                :key="index"
                                                class="flex items-center space-x-3 p-3 border rounded-md"
                                            >
                                                <div class="flex-1">
                                                    <div class="font-medium">{{ item.product_name }}</div>
                                                    <div class="text-sm text-gray-500">Price: ${{ formatPrice(item.unit_price) }}</div>
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
                                                    <div class="font-medium">${{ formatPrice(item.total_price) }}</div>
                                                </div>
                                                <button
                                                    type="button"
                                                    @click="removeItem(index)"
                                                    class="text-red-600 hover:text-red-900"
                                                >
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-else class="text-center py-8 text-gray-500">
                                        Search and select products to add to the sale
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Sale Summary & Customer Info -->
                        <div class="space-y-6">
                            <!-- Customer Information -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Customer Name
                                            </label>
                                            <input
                                                v-model="form.customer_name"
                                                type="text"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                placeholder="Optional"
                                            />
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Phone Number
                                            </label>
                                            <input
                                                v-model="form.customer_phone"
                                                type="text"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                placeholder="Optional"
                                            />
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Email
                                            </label>
                                            <input
                                                v-model="form.customer_email"
                                                type="email"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                placeholder="Optional"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sale Summary -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Sale Summary</h3>
                                    
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span>Subtotal:</span>
                                            <span>${{ formatPrice(form.subtotal) }}</span>
                                        </div>
                                        
                                        <div class="flex justify-between items-center">
                                            <label class="text-sm font-medium text-gray-700">
                                                Discount Amount:
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
                                        
                                        <div class="flex justify-between items-center">
                                            <label class="text-sm font-medium text-gray-700">
                                                Tax Amount:
                                            </label>
                                            <input
                                                v-model.number="form.tax_amount"
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                @input="calculateTotals"
                                                class="w-24 px-2 py-1 border border-gray-300 rounded text-right"
                                            />
                                        </div>
                                        
                                        <div class="border-t pt-3">
                                            <div class="flex justify-between text-lg font-bold">
                                                <span>Total:</span>
                                                <span>${{ formatPrice(form.total_amount) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Information -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Information</h3>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Payment Method *
                                            </label>
                                            <select
                                                v-model="form.payment_method"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                required
                                            >
                                                <option value="">Select Payment Method</option>
                                                <option value="cash">Cash</option>
                                                <option value="card">Card</option>
                                                <option value="bank_transfer">Bank Transfer</option>
                                                <option value="e_wallet">E-Wallet</option>
                                            </select>
                                            <div v-if="errors.payment_method" class="text-red-600 text-sm mt-1">
                                                {{ errors.payment_method }}
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Payment Received *
                                            </label>
                                            <input
                                                v-model.number="form.payment_received"
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                @input="calculateChange"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                required
                                            />
                                            <div v-if="errors.payment_received" class="text-red-600 text-sm mt-1">
                                                {{ errors.payment_received }}
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Change Amount
                                            </label>
                                            <input
                                                v-model="form.change_amount"
                                                type="number"
                                                step="0.01"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100"
                                                readonly
                                            />
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Notes
                                            </label>
                                            <textarea
                                                v-model="form.notes"
                                                rows="3"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                placeholder="Optional notes..."
                                            ></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <button
                                        type="submit"
                                        :disabled="form.items.length === 0 || form.processing"
                                        class="w-full bg-green-500 hover:bg-green-700 disabled:bg-gray-400 text-white font-bold py-3 px-4 rounded text-lg"
                                    >
                                        {{ form.processing ? 'Processing...' : 'Complete Sale' }}
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
import { ref, watch, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { debounce } from 'lodash'
import axios from 'axios'

const props = defineProps({
    errors: Object,
})

const form = useForm({
    customer_name: '',
    customer_phone: '',
    customer_email: '',
    items: [],
    subtotal: 0,
    tax_amount: 0,
    discount_amount: 0,
    total_amount: 0,
    payment_method: '',
    payment_received: 0,
    change_amount: 0,
    notes: '',
})

const productSearch = ref('')
const searchResults = ref([])

// Watch for product search changes
const debouncedProductSearch = debounce(async () => {
    if (productSearch.value.length >= 2) {
        try {
            const response = await axios.get(route('sales.search-products'), {
                params: { search: productSearch.value }
            })
            searchResults.value = response.data
        } catch (error) {
            console.error('Error searching products:', error)
            searchResults.value = []
        }
    } else {
        searchResults.value = []
    }
}, 300)

watch(productSearch, debouncedProductSearch)

// Product management functions
const addProduct = (product) => {
    const existingIndex = form.items.findIndex(item => item.product_id === product.id)
    
    if (existingIndex >= 0) {
        // Increase quantity if product already exists
        const currentQuantity = form.items[existingIndex].quantity
        if (currentQuantity < product.stock_quantity) {
            form.items[existingIndex].quantity += 1
            updateItemTotal(existingIndex)
        } else {
            alert('Cannot add more items. Stock limit reached.')
        }
    } else {
        // Add new product
        form.items.push({
            product_id: product.id,
            product_name: product.name,
            quantity: 1,
            unit_price: product.selling_price,
            total_price: product.selling_price,
            max_quantity: product.stock_quantity
        })
    }
    
    calculateTotals()
    productSearch.value = ''
    searchResults.value = []
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
        alert('Cannot add more items. Stock limit reached.')
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
    calculateTotals()
}

// Calculation functions
const calculateTotals = () => {
    form.subtotal = form.items.reduce((sum, item) => sum + item.total_price, 0)
    form.total_amount = form.subtotal + (form.tax_amount || 0) - (form.discount_amount || 0)
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
        alert('Please add at least one item to the sale.')
        return
    }
    
    form.post(route('sales.store'), {
        onSuccess: () => {
            // Handle success if needed
        }
    })
}
</script>