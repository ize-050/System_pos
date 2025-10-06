<template>
    <AppLayout title="รายละเอียดการขาย">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    การขาย #{{ sale.id.toString().padStart(6, '0') }}
                </h2>
                <div class="space-x-2">
                    <Link
                        :href="route('sales.receipt', sale.id)"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                        target="_blank"
                    >
                        พิมพ์ใบเสร็จ
                    </Link>
                    <Link
                        v-if="$page.props.auth.user.role === 'admin' || $page.props.auth.user.role === 'manager'"
                        :href="route('sales.edit', sale.id)"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                    >
                        แก้ไขการขาย
                    </Link>
                    <Link
                        :href="route('sales.index')"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                    >
                        กลับไปยังรายการขาย
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Sale Details -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Sale Overview -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">ภาพรวมการขาย</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">รหัสการขาย</label>
                                        <p class="mt-1 text-sm text-gray-900">#{{ sale.id.toString().padStart(6, '0') }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">วันที่ขาย</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ formatDate(sale.created_at) }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">พนักงานขาย</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ sale.cashier?.name || 'ไม่ระบุ' }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">วิธีการชำระเงิน</label>
                                        <span :class="getPaymentMethodClass(sale.payment_method)" class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                            {{ formatPaymentMethod(sale.payment_method) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">ข้อมูลลูกค้า</h3>
                                
                                <div v-if="sale.customer_name || sale.customer_phone || sale.customer_email" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div v-if="sale.customer_name">
                                        <label class="block text-sm font-medium text-gray-700">ชื่อ</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ sale.customer_name }}</p>
                                    </div>
                                    
                                    <div v-if="sale.customer_phone">
                                        <label class="block text-sm font-medium text-gray-700">เบอร์โทรศัพท์</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ sale.customer_phone }}</p>
                                    </div>
                                    
                                    <div v-if="sale.customer_email">
                                        <label class="block text-sm font-medium text-gray-700">อีเมล</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ sale.customer_email }}</p>
                                    </div>
                                </div>
                                
                                <div v-else class="text-gray-500 text-sm">
                                    ลูกค้าทั่วไป (ไม่มีข้อมูลลูกค้า)
                                </div>
                            </div>
                        </div>

                        <!-- Sale Items -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">รายการสินค้า</h3>
                                
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    สินค้า
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    ราคาต่อหน่วย
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    จำนวน
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    รวม
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="item in sale.items" :key="item.id">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div v-if="item.product?.image" class="flex-shrink-0 h-10 w-10">
                                                            <img class="h-10 w-10 rounded-full object-cover" :src="item.product.image" :alt="item.product.name" />
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                {{ item.product?.name || 'ไม่พบสินค้า' }}
                                                            </div>
                                                            <div v-if="item.product?.category" class="text-sm text-gray-500">
                                                                {{ item.product.category.name }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    ฿{{ formatPrice(item.unit_price) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ item.quantity }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    ฿{{ formatPrice(item.total_price) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div v-if="sale.notes" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">หมายเหตุ</h3>
                                <p class="text-sm text-gray-700">{{ sale.notes }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Financial Summary -->
                    <div class="space-y-6">
                        <!-- Financial Summary -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">สรุปการเงิน</h3>
                                
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">ยอดรวมย่อย:</span>
                                        <span class="text-sm font-medium text-gray-900">฿{{ formatPrice(sale.subtotal) }}</span>
                                    </div>
                                    
                                    <div v-if="sale.discount_amount > 0" class="flex justify-between">
                                        <span class="text-sm text-gray-600">ส่วนลด:</span>
                                        <span class="text-sm font-medium text-red-600">-฿{{ formatPrice(sale.discount_amount) }}</span>
                                    </div>
                                    
                                    <div v-if="sale.tax_amount > 0" class="flex justify-between">
                                        <span class="text-sm text-gray-600">ภาษี:</span>
                                        <span class="text-sm font-medium text-gray-900">฿{{ formatPrice(sale.tax_amount) }}</span>
                                    </div>
                                    
                                    <div class="border-t pt-3">
                                        <div class="flex justify-between">
                                            <span class="text-base font-medium text-gray-900">ยอดรวมทั้งหมด:</span>
                                            <span class="text-base font-bold text-gray-900">฿{{ formatPrice(sale.total_amount) }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="border-t pt-3 space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">จำนวนเงินที่ได้รับ:</span>
                                            <span class="text-sm font-medium text-green-600">฿{{ formatPrice(sale.payment_received) }}</span>
                                        </div>
                                        
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">เงินทอน:</span>
                                            <span class="text-sm font-medium text-blue-600">฿{{ formatPrice(sale.change_amount) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sale Statistics -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">สถิติการขาย</h3>
                                
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">จำนวนรายการสินค้า:</span>
                                        <span class="text-sm font-medium text-gray-900">{{ sale.items?.length || 0 }}</span>
                                    </div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">จำนวนสินค้าทั้งหมด:</span>
                                        <span class="text-sm font-medium text-gray-900">{{ getTotalQuantity() }}</span>
                                    </div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">ราคาเฉลี่ยต่อรายการ:</span>
                                        <span class="text-sm font-medium text-gray-900">฿{{ formatPrice(getAverageItemPrice()) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Timestamps -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">ข้อมูลเวลา</h3>
                                
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">วันที่สร้าง</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ formatDateTime(sale.created_at) }}</p>
                                    </div>
                                    
                                    <div v-if="sale.updated_at !== sale.created_at">
                                        <label class="block text-sm font-medium text-gray-700">วันที่อัปเดตล่าสุด</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ formatDateTime(sale.updated_at) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">การดำเนินการ</h3>
                                
                                <div class="space-y-3">
                                    <Link
                                        :href="route('sales.receipt', sale.id)"
                                        class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-center block transition ease-in-out duration-150"
                                        style="background-color: #6B7B47;"
                                        onmouseover="this.style.backgroundColor='#8A9B5A'"
                                        onmouseout="this.style.backgroundColor='#6B7B47'"
                                        target="_blank"
                                    >
                                        พิมพ์ใบเสร็จ
                                    </Link>
                                    
                                    <Link
                                        v-if="$page.props.auth.user.role === 'admin' || $page.props.auth.user.role === 'manager'"
                                        :href="route('sales.edit', sale.id)"
                                        class="w-full text-white font-bold py-2 px-4 rounded text-center block transition ease-in-out duration-150"
                                         style="background-color: #6B7B47;"
                                         onmouseover="this.style.backgroundColor='#8A9B5A'"
                                         onmouseout="this.style.backgroundColor='#6B7B47'"
                                    >
                                        แก้ไขการขาย
                                    </Link>
                                    
                                    <button
                                        v-if="$page.props.auth.user.role === 'admin'"
                                        @click="deleteSale()"
                                        class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                    >
                                        ลบการขาย
                                    </button>
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
import { router } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    sale: Object,
})

// Utility functions
const formatPrice = (price) => {
    return parseFloat(price || 0).toFixed(2)
}

const formatPaymentMethod = (method) => {
    const methods = {
        cash: 'เงินสด',
        card: 'บัตรเครดิต/เดบิต',
        bank_transfer: 'โอนเงิน',
        e_wallet: 'กระเป๋าเงินอิเล็กทรอนิกส์'
    }
    return methods[method] || method
}

const getPaymentMethodClass = (method) => {
    const classes = {
        cash: 'bg-green-100 text-green-800',
        card: 'bg-blue-100 text-blue-800',
        bank_transfer: 'bg-purple-100 text-purple-800',
        e_wallet: 'bg-orange-100 text-orange-800'
    }
    return classes[method] || 'bg-gray-100 text-gray-800'
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatDateTime = (date) => {
    return new Date(date).toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const getTotalQuantity = () => {
    return props.sale.items?.reduce((total, item) => total + item.quantity, 0) || 0
}

const getAverageItemPrice = () => {
    const items = props.sale.items || []
    if (items.length === 0) return 0
    
    const totalPrice = items.reduce((total, item) => total + item.total_price, 0)
    const totalQuantity = getTotalQuantity()
    
    return totalQuantity > 0 ? totalPrice / totalQuantity : 0
}

// Print receipt function
const printReceipt = () => {
    window.print()
}

// Delete sale function
const deleteSale = () => {
    if (confirm('คุณแน่ใจหรือไม่ที่จะลบการขายนี้? การดำเนินการนี้ไม่สามารถยกเลิกได้')) {
        router.delete(route('sales.destroy', props.sale.id), {
            onSuccess: () => {
                router.visit(route('sales.index'))
            }
        })
    }
}
</script>