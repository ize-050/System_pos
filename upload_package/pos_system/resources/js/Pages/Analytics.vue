<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                การวิเคราะห์และรายงานเชิงลึก
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">วันที่เริ่มต้น</label>
                                <input 
                                    type="date" 
                                    v-model="filters.startDate"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">วันที่สิ้นสุด</label>
                                <input 
                                    type="date" 
                                    v-model="filters.endDate"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">ประเภทการวิเคราะห์</label>
                                <select 
                                    v-model="filters.analysisType"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="daily">รายวัน</option>
                                    <option value="weekly">รายสัปดาห์</option>
                                    <option value="monthly">รายเดือน</option>
                                    <option value="yearly">รายปี</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button 
                                    @click="loadAnalytics"
                                    :disabled="loading"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                                >
                                    <span v-if="loading">กำลังโหลด...</span>
                                    <span v-else>วิเคราะห์</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Key Metrics Overview -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-lg shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm">ยอดขายรวม</p>
                                <p class="text-2xl font-bold">{{ formatCurrency(analytics?.total_sales || 0) }}</p>
                                <p class="text-blue-100 text-xs mt-1">
                                    {{ (analytics?.sales_growth || 0) >= 0 ? '↗️' : '↘️' }} 
                                    {{ Math.abs(analytics?.sales_growth || 0) }}% จากช่วงก่อน
                                </p>
                            </div>
                            <div class="text-3xl">💰</div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-lg shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm">กำไรขั้นต้น</p>
                                <p class="text-2xl font-bold">{{ formatCurrency(analytics?.gross_profit || 0) }}</p>
                                <p class="text-green-100 text-xs mt-1">
                                    Margin: {{ analytics?.profit_margin || 0 }}%
                                </p>
                            </div>
                            <div class="text-3xl">📈</div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-lg shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm">จำนวนลูกค้า</p>
                                <p class="text-2xl font-bold">{{ analytics?.total_customers || 0 }}</p>
                                <p class="text-purple-100 text-xs mt-1">
                                    ลูกค้าใหม่: {{ analytics?.new_customers || 0 }}
                                </p>
                            </div>
                            <div class="text-3xl">👥</div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-6 rounded-lg shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm">ค่าเฉลี่ยต่อออเดอร์</p>
                                <p class="text-2xl font-bold">{{ formatCurrency(analytics?.avg_order_value || 0) }}</p>
                                <p class="text-orange-100 text-xs mt-1">
                                    จำนวนออเดอร์: {{ analytics?.total_orders || 0 }}
                                </p>
                            </div>
                            <div class="text-3xl">🛒</div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6" v-if="analytics">
                    <!-- Sales Trend Chart -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">แนวโน้มยอดขาย</h3>
                            <div class="h-64">
                                <SalesTrendChart 
                                    :data="analytics.sales_trend || []"
                                    :analysis-type="filters.analysisType"
                                    :height="256"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Customer Segmentation Chart -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">การแบ่งกลุ่มลูกค้า</h3>
                            <div class="h-64">
                                <CustomerSegmentChart 
                                    :vip-customers="analytics.vip_customers || 0"
                                    :regular-customers="analytics.regular_customers || 0"
                                    :new-customers="analytics.new_customers || 0"
                                    :height="256"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Performance Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6" v-if="analytics">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">ประสิทธิภาพสินค้า</h3>
                        <div class="h-80">
                            <ProductPerformanceChart 
                                :top-products="analytics.top_products || []"
                                :height="320"
                            />
                        </div>
                    </div>
                </div>

                <!-- Profit Analysis Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6" v-if="analytics">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">การวิเคราะห์กำไรขั้นต้น</h3>
                        <div class="h-80">
                            <ProfitAnalysisChart 
                                :data="analytics.sales_trend || []"
                                :analysis-type="filters.analysisType"
                                :height="320"
                            />
                        </div>
                    </div>
                </div>

                <!-- Detailed Analysis Tables -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
                    <!-- Top Selling Products -->
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">🥇 สินค้าขายดี Top 10</h3>
                            <span class="text-sm text-gray-500">{{ filters.analysis_type }}</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สินค้า</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ขายได้</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รายได้</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">กำไร</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(product, index) in (analytics?.top_products || [])" :key="product.id">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-indigo-100 text-indigo-800 text-sm font-medium">
                                                        {{ index + 1 }}
                                                    </span>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                                                    <div class="text-sm text-gray-500">{{ product.category_name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ product.total_sold }} ชิ้น
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatCurrency(product.total_revenue) }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatCurrency(product.gross_profit) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Slow Moving Products -->
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">🐌 สินค้าขายช้า</h3>
                            <span class="text-sm text-gray-500">ต้องปรับกลยุทธ์</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สินค้า</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">คงเหลือ</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ขายได้</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">วันที่ขายล่าสุด</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="product in (analytics?.slow_products || [])" :key="product.id">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-red-100 text-red-800 text-sm font-medium">
                                                        ⚠️
                                                    </span>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                                                    <div class="text-sm text-gray-500">{{ product.category_name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ product.current_stock }} ชิ้น
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ product.total_sold || 0 }} ชิ้น
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ product.last_sale_date ? formatDate(product.last_sale_date) : 'ไม่เคยขาย' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Customer Analysis -->
                <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">👥 การวิเคราะห์พฤติกรรมลูกค้า</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <!-- Customer Segmentation Chart -->
                        <div class="col-span-2">
                            <h4 class="text-md font-medium text-gray-700 mb-3">การแบ่งกลุ่มลูกค้า</h4>
                            <div class="h-64">
                                <canvas ref="customerSegmentChart"></canvas>
                            </div>
                        </div>
                        
                        <!-- Customer Stats -->
                        <div class="space-y-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-sm text-blue-600 font-medium">ลูกค้า VIP</div>
                                <div class="text-2xl font-bold text-blue-900">{{ analytics?.vip_customers || 0 }}</div>
                                <div class="text-xs text-blue-600">ซื้อมากกว่า {{ formatCurrency(analytics?.vip_threshold || 0) }}</div>
                            </div>
                            
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="text-sm text-green-600 font-medium">ลูกค้าประจำ</div>
                                <div class="text-2xl font-bold text-green-900">{{ analytics?.regular_customers || 0 }}</div>
                                <div class="text-xs text-green-600">ซื้อ 3+ ครั้ง</div>
                            </div>
                            
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <div class="text-sm text-yellow-600 font-medium">ลูกค้าใหม่</div>
                                <div class="text-2xl font-bold text-yellow-900">{{ analytics?.new_customers || 0 }}</div>
                                <div class="text-xs text-yellow-600">ซื้อครั้งแรก</div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Customers Table -->
                    <div class="overflow-x-auto">
                        <h4 class="text-md font-medium text-gray-700 mb-3">🏆 ลูกค้าที่ซื้อมากที่สุด</h4>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ลูกค้า</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ยอดซื้อรวม</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">จำนวนครั้ง</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ค่าเฉลี่ย/ครั้ง</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ซื้อล่าสุด</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="(customer, index) in (analytics?.top_customers || [])" :key="customer.id">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-purple-100 text-purple-800 text-sm font-medium">
                                                    {{ index + 1 }}
                                                </span>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ customer.name }}</div>
                                                <div class="text-sm text-gray-500">{{ customer.phone }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatCurrency(customer.total_spent) }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ customer.total_orders }} ครั้ง
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatCurrency(customer.avg_order_value) }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(customer.last_purchase) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                    <span class="ml-3 text-gray-600">กำลังโหลดข้อมูล...</span>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import axios from 'axios'
import SalesTrendChart from '@/Components/Charts/SalesTrendChart.vue'
import ProductPerformanceChart from '@/Components/Charts/ProductPerformanceChart.vue'
import CustomerSegmentChart from '@/Components/Charts/CustomerSegmentChart.vue'
import ProfitAnalysisChart from '@/Components/Charts/ProfitAnalysisChart.vue'

// Reactive data
const loading = ref(false)
const analytics = ref(null)
const filters = ref({
    startDate: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
    endDate: new Date().toISOString().split('T')[0],
    analysisType: 'daily'
})

// Chart references
const salesTrendChart = ref(null)
const productPerformanceChart = ref(null)
const customerSegmentChart = ref(null)

// Chart instances
let salesChart = null
let productChart = null
let customerChart = null

// Methods
const loadAnalytics = async () => {
    loading.value = true
    try {
        const response = await axios.get('/api/analytics/comprehensive', {
            params: filters.value
        })
        analytics.value = response.data
    } catch (error) {
        console.error('Error loading analytics:', error)
        alert('เกิดข้อผิดพลาดในการโหลดข้อมูล')
    } finally {
        loading.value = false
    }
}

const initializeCharts = () => {
    // Destroy existing charts
    if (salesChart) salesChart.destroy()
    if (productChart) productChart.destroy()
    if (customerChart) customerChart.destroy()

    // Sales Trend Chart
    if (salesTrendChart.value) {
        const ctx = salesTrendChart.value.getContext('2d')
        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: (analytics.value?.sales_trend || []).map(item => item.period),
                datasets: [
                    {
                        label: 'ยอดขาย',
                        data: (analytics.value?.sales_trend || []).map(item => item.total_sales),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        yAxisID: 'y'
                    },
                    {
                        label: 'กำไรขั้นต้น',
                        data: (analytics.value?.sales_trend || []).map(item => item.gross_profit),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        yAxisID: 'y'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                    }
                }
            }
        })
    }

    // Product Performance Chart
    if (productPerformanceChart.value) {
        const ctx = productPerformanceChart.value.getContext('2d')
        productChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: (analytics.value?.top_products || []).slice(0, 5).map(item => item.name),
                datasets: [{
                    label: 'ยอดขาย (บาท)',
                    data: (analytics.value?.top_products || []).slice(0, 5).map(item => item.total_revenue),
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(168, 85, 247, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        })
    }

    // Customer Segment Chart
    if (customerSegmentChart.value) {
        const ctx = customerSegmentChart.value.getContext('2d')
        customerChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['ลูกค้า VIP', 'ลูกค้าประจำ', 'ลูกค้าใหม่'],
                datasets: [{
                    data: [
                        analytics.value?.vip_customers || 0,
                        analytics.value?.regular_customers || 0,
                        analytics.value?.new_customers || 0
                    ],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(251, 191, 36, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        })
    }
}

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('th-TH', {
        style: 'currency',
        currency: 'THB'
    }).format(amount || 0)
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('th-TH')
}

const exportReport = async (format) => {
    try {
        loading.value = true
        
        const response = await axios.get('/api/analytics/export', {
            params: {
                format: format,
                start_date: filters.value.startDate,
                end_date: filters.value.endDate,
                analysis_type: filters.value.analysisType
            }
        })

        if (format === 'pdf') {
            // Create a new window with the HTML content for PDF printing
            const printWindow = window.open('', '_blank')
            printWindow.document.write(response.data.html)
            printWindow.document.close()
            printWindow.focus()
            printWindow.print()
        } else if (format === 'excel') {
            // Convert data to CSV format and download
            const csvContent = convertToCSV(response.data.data)
            downloadCSV(csvContent, `analytics-report-${filters.value.startDate}-to-${filters.value.endDate}.csv`)
        }
    } catch (error) {
        console.error('Export error:', error)
        alert('เกิดข้อผิดพลาดในการ export รายงาน')
    } finally {
        loading.value = false
    }
}

const convertToCSV = (data) => {
    let csv = 'รายงานการวิเคราะห์เชิงลึก\n\n'
    
    // Basic metrics
    csv += 'ข้อมูลพื้นฐาน\n'
    csv += `ยอดขายรวม,${data.total_sales}\n`
    csv += `กำไรขั้นต้น,${data.gross_profit}\n`
    csv += `ลูกค้าทั้งหมด,${data.total_customers}\n`
    csv += `ค่าเฉลี่ยต่อออเดอร์,${data.average_order_value}\n\n`
    
    // Top products
    csv += 'สินค้าขายดี Top 10\n'
    csv += 'ชื่อสินค้า,จำนวนขาย,รายได้,กำไร\n'
    data.top_products.forEach(product => {
        csv += `${product.name},${product.total_quantity},${product.total_revenue},${product.total_profit}\n`
    })
    
    csv += '\n'
    
    // Slow products
    csv += 'สินค้าขายช้า\n'
    csv += 'ชื่อสินค้า,จำนวนขาย,รายได้,กำไร\n'
    data.slow_products.forEach(product => {
        csv += `${product.name},${product.total_quantity},${product.total_revenue},${product.total_profit}\n`
    })
    
    return csv
}

const downloadCSV = (csvContent, filename) => {
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
    const link = document.createElement('a')
    
    if (link.download !== undefined) {
        const url = URL.createObjectURL(blob)
        link.setAttribute('href', url)
        link.setAttribute('download', filename)
        link.style.visibility = 'hidden'
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
    }
}

// Load data on component mount
onMounted(() => {
    loadAnalytics()
})
</script>

<style scoped>
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>