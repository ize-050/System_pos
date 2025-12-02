<template>
    <AppLayout title="แดชบอร์ด - POS System">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl leading-tight" style="color: #2D3748;">
                    🏪 แดชบอร์ด POS System
                </h2>
                <div class="flex space-x-2">
                    <input
                        v-model="filters.start_date"
                        type="date"
                        class="border rounded-md shadow-sm focus:outline-none focus:ring-2"
                        style="border-color: #E2E8F0; --tw-ring-color: #6B7B47;"
                        @change="updateFilters"
                    />
                    <input
                        v-model="filters.end_date"
                        type="date"
                        class="border rounded-md shadow-sm focus:outline-none focus:ring-2"
                        style="border-color: #E2E8F0; --tw-ring-color: #6B7B47;"
                        @change="updateFilters"
                    />
                </div>
            </div>
        </template>

        <div class="py-3 sm:py-4 lg:py-6">
            <div class="max-w-full mx-auto px-3 sm:px-4 lg:px-6">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-4 sm:mb-6 lg:mb-8">
                    <!-- Total Sales -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100 hover:shadow-md transition-all duration-200">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 sm:ml-5">
                                    <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">ยอดขายรวม</p>
                                    <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">
                                        ฿{{ formatPrice(statistics?.total_sales || 0) }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-3 sm:mt-4">
                                <div class="flex items-center text-sm">
                                    <span :class="(statistics?.monthly_growth || 0) >= 0 ? 'text-green-600' : 'text-red-600'" class="flex items-center font-medium">
                                        <svg v-if="(statistics?.monthly_growth || 0) >= 0" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                        </svg>
                                        <svg v-else class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                        </svg>
                                        {{ Math.abs(statistics?.monthly_growth || 0) }}%
                                    </span>
                                    <span class="text-gray-500 ml-2">เทียบกับเดือนที่แล้ว</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Orders -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100 hover:shadow-md transition-all duration-200">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 sm:ml-5">
                                    <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">จำนวนออเดอร์</p>
                                    <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">
                                        {{ statistics?.total_orders || 0 }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Products -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100 hover:shadow-md transition-all duration-200">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 sm:ml-5">
                                    <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">จำนวนสินค้า</p>
                                    <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">
                                        {{ statistics?.total_products || 0 }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Low Stock Alert -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100 hover:shadow-md transition-all duration-200">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 sm:ml-5">
                                    <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">สินค้าใกล้หมด</p>
                                    <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">
                                        {{ statistics?.low_stock_products || 0 }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 sm:gap-8 mb-6 sm:mb-8">
                    <!-- Sales Trend Chart -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
                        <div class="p-4 sm:p-6">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">📈 แนวโน้มการขาย (7 วันล่าสุด)</h3>
                            <div class="h-48 sm:h-64">
                                <canvas ref="salesChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Top Selling Products Chart -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
                        <div class="p-4 sm:p-6">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">🏆 สินค้าที่ขายดี</h3>
                            <div class="h-48 sm:h-64">
                                <canvas ref="paymentChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
                    <!-- Top Selling Products -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
                        <div class="p-4 sm:p-6">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">🏆 สินค้าขายดี</h3>
                            <div class="space-y-3">
                                <div v-for="product in top_products" :key="product.name" class="flex items-center justify-between p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center">
                                        <div v-if="product.image" class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full object-cover" :src="product.image" :alt="product.name" />
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                                            <div class="text-xs text-gray-500">ขายแล้ว {{ product.total_sold }} ชิ้น</div>
                                        </div>
                                    </div>
                                    <div class="text-sm font-semibold text-gray-900">
                                        ฿{{ formatPrice(product.total_revenue) }}
                                    </div>
                                </div>
                                <div v-if="!top_products || top_products.length === 0" class="text-center text-gray-500 py-8">
                                    ไม่มีข้อมูลการขาย
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Sales -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
                        <div class="p-4 sm:p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-base sm:text-lg font-semibold text-gray-900">🕒 การขายล่าสุด</h3>
                                <Link :href="route('sales.index')" class="text-sm font-medium transition-colors" style="color: #6B7B47;" onmouseover="this.style.color='#8A9B5A'" onmouseout="this.style.color='#6B7B47'">
                                    ดูทั้งหมด
                                </Link>
                            </div>
                            <div class="space-y-3">
                                <div v-for="sale in recent_sales" :key="sale.id" class="flex items-center justify-between p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            การขาย #{{ sale.id.toString().padStart(6, '0') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ sale.cashier?.name || 'ไม่ระบุ' }} • {{ formatDate(sale.created_at) }}
                                        </div>
                                    </div>
                                    <div class="text-sm font-semibold text-gray-900">
                                        ฿{{ formatPrice(sale.total_amount) }}
                                    </div>
                                </div>
                                <div v-if="!recent_sales || recent_sales.length === 0" class="text-center text-gray-500 py-8">
                                    ไม่มีการขายล่าสุด
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Alerts -->
                <div v-if="low_stock_items && low_stock_items.length > 0" class="mt-4 sm:mt-6 lg:mt-8">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
                        <div class="p-4 sm:p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-base sm:text-lg font-semibold text-gray-900">⚠️ แจ้งเตือนสินค้าใกล้หมด</h3>
                                <Link :href="route('products.index')" class="text-sm font-medium transition-colors" style="color: #6B7B47;" onmouseover="this.style.color='#8A9B5A'" onmouseout="this.style.color='#6B7B47'">
                                    จัดการสินค้า
                                </Link>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                สินค้า
                                            </th>
                                            <th class="hidden sm:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                หมวดหมู่
                                            </th>
                                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                สต็อกปัจจุบัน
                                            </th>
                                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                สถานะ
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="item in low_stock_items" :key="item.id" class="hover:bg-gray-50 transition-colors">
                                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div v-if="item.image" class="flex-shrink-0 h-8 w-8 sm:h-10 sm:w-10">
                                                        <img class="h-8 w-8 sm:h-10 sm:w-10 rounded-full object-cover" :src="item.image" :alt="item.name" />
                                                    </div>
                                                    <div class="ml-2 sm:ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ item.category?.name || 'ไม่ระบุ' }}
                                            </td>
                                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ item.stocks?.[0]?.quantity || 0 }}
                                            </td>
                                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                                <span :class="getStockStatusClass(item.stocks?.[0]?.quantity || 0)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                    {{ getStockStatus(item.stocks?.[0]?.quantity || 0) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const props = defineProps({
    statistics: Object,
    sales_trend: Array,
    top_products: Array,
    payment_methods: Array,
    recent_sales: Array,
    low_stock_items: Array,
    filters: Object,
})

const filters = ref({
    start_date: props.filters?.start_date || new Date().toISOString().split('T')[0],
    end_date: props.filters?.end_date || new Date().toISOString().split('T')[0],
})

const salesChart = ref(null)
const paymentChart = ref(null)
let salesChartInstance = null
let paymentChartInstance = null

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

const getStockStatus = (quantity) => {
    if (quantity <= 0) return 'หมด'
    if (quantity <= 5) return 'วิกฤต'
    if (quantity <= 10) return 'ใกล้หมด'
    return 'มีสต็อก'
}

const getStockStatusClass = (quantity) => {
    if (quantity <= 0) return 'bg-red-100 text-red-800'
    if (quantity <= 5) return 'bg-red-100 text-red-800'
    if (quantity <= 10) return 'bg-yellow-100 text-yellow-800'
    return 'bg-green-100 text-green-800'
}

const updateFilters = () => {
    router.get(route('dashboard'), filters.value, {
        preserveState: true,
        preserveScroll: true,
    })
}

const createSalesChart = () => {
    if (!salesChart.value || !props.sales_trend || props.sales_trend.length === 0) return

    const ctx = salesChart.value.getContext('2d')
    
    // Destroy existing chart
    if (salesChartInstance) {
        salesChartInstance.destroy()
    }

    // Prepare data for last 7 days
    const last7Days = []
    const today = new Date()
    
    for (let i = 6; i >= 0; i--) {
        const date = new Date(today)
        date.setDate(date.getDate() - i)
        last7Days.push(date.toISOString().split('T')[0])
    }

    // Map sales data to last 7 days
    const salesData = last7Days.map(date => {
        const found = props.sales_trend.find(item => item.date === date)
        return found ? parseFloat(found.total) : 0
    })

    const labels = last7Days.map(date => {
        const d = new Date(date)
        return d.toLocaleDateString('th-TH', { day: '2-digit', month: 'short' })
    })

    salesChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'ยอดขาย (บาท)',
                data: salesData,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    callbacks: {
                        label: function(context) {
                            return 'ยอดขาย: ฿' + new Intl.NumberFormat('th-TH').format(context.parsed.y)
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    ticks: {
                        callback: function(value) {
                            return '฿' + new Intl.NumberFormat('th-TH').format(value)
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    })
}

const createPaymentChart = () => {
    if (!paymentChart.value || !props.top_products || props.top_products.length === 0) return

    const ctx = paymentChart.value.getContext('2d')
    
    // Destroy existing chart
    if (paymentChartInstance) {
        paymentChartInstance.destroy()
    }

    // Use top 5 products for donut chart
    const topProducts = props.top_products.slice(0, 5)
    const labels = topProducts.map(product => product.name)
    const data = topProducts.map(product => parseFloat(product.total_revenue))
    
    const colors = [
        'rgba(59, 130, 246, 0.8)',
        'rgba(16, 185, 129, 0.8)',
        'rgba(251, 191, 36, 0.8)',
        'rgba(239, 68, 68, 0.8)',
        'rgba(168, 85, 247, 0.8)'
    ]

    paymentChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderColor: colors.map(color => color.replace('0.8', '1')),
                borderWidth: 2,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    callbacks: {
                        label: function(context) {
                            const label = context.label || ''
                            const value = context.parsed
                            const total = context.dataset.data.reduce((a, b) => a + b, 0)
                            const percentage = ((value / total) * 100).toFixed(1)
                            return label + ': ฿' + new Intl.NumberFormat('th-TH').format(value) + ' (' + percentage + '%)'
                        }
                    }
                }
            }
        }
    })
}

onMounted(() => {
    nextTick(() => {
        try {
            createSalesChart()
            createPaymentChart()
        } catch (error) {
            console.error('Error creating charts:', error)
        }
    })
})
</script>
