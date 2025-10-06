<template>
    <AppLayout title="Inventory Report">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Inventory Report
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <select
                                    v-model="form.category_id"
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option value="">All Categories</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Status</label>
                                <select
                                    v-model="form.stock_status"
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option value="">All Status</option>
                                    <option value="in_stock">In Stock</option>
                                    <option value="low_stock">Low Stock</option>
                                    <option value="out_of_stock">Out of Stock</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                                <select
                                    v-model="form.sort_by"
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option value="name">Product Name</option>
                                    <option value="quantity">Stock Quantity</option>
                                    <option value="value">Stock Value</option>
                                    <option value="category">Category</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button
                                    @click="generateReport"
                                    type="button"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                >
                                    Generate Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Total Products
                                        </dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            {{ summary?.total_products || 0 }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Total Stock Value
                                        </dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            ${{ formatPrice(summary?.total_stock_value || 0) }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Low Stock Items
                                        </dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            {{ summary?.low_stock_items || 0 }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Out of Stock
                                        </dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            {{ summary?.out_of_stock_items || 0 }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Distribution Chart -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Stock Status Distribution</h3>
                            <div class="h-64 flex items-center justify-center">
                                <div v-if="stockStatusChart && stockStatusChart.length > 0" class="w-full h-full">
                                    <canvas ref="stockStatusChartRef"></canvas>
                                </div>
                                <div v-else class="text-gray-500">
                                    No stock data available
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Stock Value by Category</h3>
                            <div class="h-64 flex items-center justify-center">
                                <div v-if="categoryChart && categoryChart.length > 0" class="w-full h-full">
                                    <canvas ref="categoryChartRef"></canvas>
                                </div>
                                <div v-else class="text-gray-500">
                                    No category data available
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Inventory Report Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Inventory Details</h3>
                            <div class="flex space-x-2">
                                <button
                                    @click="exportReport"
                                    type="button"
                                    :style="{
                                        backgroundColor: '#6B7B47',
                                        borderColor: '#6B7B47'
                                    }"
                                    @mouseover="$event.target.style.backgroundColor = '#8A9B5A'"
                                    @mouseout="$event.target.style.backgroundColor = '#6B7B47'"
                                    class="px-4 py-2 text-white rounded-lg hover:shadow-md transition-all duration-200 font-bold focus:outline-none focus:shadow-outline"
                                >
                                    Export CSV
                                </button>
                                <button
                                    @click="printReport"
                                    type="button"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                >
                                    Print Report
                                </button>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Product
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Category
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Current Stock
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Min Stock
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Unit Price
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Stock Value
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Last Updated
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="item in reportData" :key="item.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div v-if="item.image" class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full object-cover" :src="item.image" :alt="item.name" />
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                                                    <div class="text-sm text-gray-500">SKU: {{ item.sku }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ item.category?.name || 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ item.current_stock || 0 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ item.min_stock || 0 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ${{ formatPrice(item.selling_price || 0) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ${{ formatPrice(item.stock_value || 0) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getStockStatusClass(item.current_stock || 0, item.min_stock || 0)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ getStockStatus(item.current_stock || 0, item.min_stock || 0) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(item.updated_at) }}
                                        </td>
                                    </tr>
                                    <tr v-if="!reportData || reportData.length === 0">
                                        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No inventory data available for the selected criteria
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="reportData && reportData.length > 0" class="mt-6">
                            <nav class="flex items-center justify-between">
                                <div class="flex-1 flex justify-between sm:hidden">
                                    <button
                                        :disabled="!pagination?.prev_page_url"
                                        @click="changePage(pagination.current_page - 1)"
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        Previous
                                    </button>
                                    <button
                                        :disabled="!pagination?.next_page_url"
                                        @click="changePage(pagination.current_page + 1)"
                                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        Next
                                    </button>
                                </div>
                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm text-gray-700">
                                            Showing {{ pagination?.from || 0 }} to {{ pagination?.to || 0 }} of {{ pagination?.total || 0 }} results
                                        </p>
                                    </div>
                                    <div>
                                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                            <button
                                                :disabled="!pagination?.prev_page_url"
                                                @click="changePage(pagination.current_page - 1)"
                                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                            >
                                                Previous
                                            </button>
                                            <button
                                                :disabled="!pagination?.next_page_url"
                                                @click="changePage(pagination.current_page + 1)"
                                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                            >
                                                Next
                                            </button>
                                        </nav>
                                    </div>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    summary: Object,
    reportData: Array,
    stockStatusChart: Array,
    categoryChart: Array,
    categories: Array,
    pagination: Object,
    filters: Object,
})

const form = reactive({
    category_id: props.filters?.category_id || '',
    stock_status: props.filters?.stock_status || '',
    sort_by: props.filters?.sort_by || 'name',
    page: props.filters?.page || 1,
})

const stockStatusChartRef = ref(null)
const categoryChartRef = ref(null)

// Utility functions
const formatPrice = (price) => {
    return parseFloat(price || 0).toFixed(2)
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const getStockStatus = (currentStock, minStock) => {
    if (currentStock <= 0) return 'Out of Stock'
    if (currentStock <= minStock) return 'Critical'
    if (currentStock <= minStock * 2) return 'Low Stock'
    return 'In Stock'
}

const getStockStatusClass = (currentStock, minStock) => {
    if (currentStock <= 0) return 'bg-red-100 text-red-800'
    if (currentStock <= minStock) return 'bg-red-100 text-red-800'
    if (currentStock <= minStock * 2) return 'bg-yellow-100 text-yellow-800'
    return 'bg-green-100 text-green-800'
}

const generateReport = () => {
    form.page = 1 // Reset to first page when generating new report
    router.get(route('reports.inventory'), form, {
        preserveState: true,
        preserveScroll: true,
    })
}

const changePage = (page) => {
    form.page = page
    router.get(route('reports.inventory'), form, {
        preserveState: true,
        preserveScroll: true,
    })
}

const exportReport = () => {
    const params = new URLSearchParams(form)
    params.append('export', 'csv')
    window.open(`${route('reports.inventory')}?${params.toString()}`, '_blank')
}

const printReport = () => {
    window.print()
}

onMounted(() => {
    // Chart initialization code would go here
    console.log('Inventory Report mounted with data:', props)
})
</script>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    .print-break {
        page-break-after: always;
    }
}
</style>