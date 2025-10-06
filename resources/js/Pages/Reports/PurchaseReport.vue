<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PurchaseReportFilters from '@/Components/Reports/PurchaseReportFilters.vue';
import ReportTable from '@/Components/Reports/ReportTable.vue';

const props = defineProps({
    purchaseOrders: Object,
    summary: Object,
    suppliers: Array,
    filters: Object,
});

const exporting = ref(false);

const columns = [
    { key: 'po_number', label: 'เลขที่ใบสั่งซื้อ', sortable: true },
    { key: 'supplier.name', label: 'ซัพพลายเออร์', sortable: true },
    { key: 'order_date', label: 'วันที่สั่งซื้อ', sortable: true },
    { key: 'subtotal', label: 'ยอดรวม', sortable: true, format: 'currency' },
    { key: 'vat_amount', label: 'VAT 7%', sortable: true, format: 'currency' },
    { key: 'total_amount', label: 'ยอดรวมสุทธิ', sortable: true, format: 'currency' },
    { key: 'status', label: 'สถานะ', sortable: true, format: 'status' },
];

const handleFilter = (filters) => {
    router.get(route('reports.purchases'), filters, {
        preserveState: true,
        preserveScroll: true,
    });
};

const handleExport = async () => {
    exporting.value = true;
    
    try {
        const response = await axios.post(route('reports.export'), {
            report_type: 'purchase',
            format: 'xlsx',
            filters: props.filters,
        });

        if (response.data.success) {
            window.location.href = response.data.download_url;
        }
    } catch (error) {
        alert('เกิดข้อผิดพลาดในการ Export: ' + (error.response?.data?.message || error.message));
    } finally {
        exporting.value = false;
    }
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('th-TH', {
        style: 'currency',
        currency: 'THB',
    }).format(value);
};
</script>

<template>
    <AppLayout title="รายงานยอดซื้อ">
        <Head title="รายงานยอดซื้อ" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">รายงานยอดซื้อ</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            รายงานการสั่งซื้อสินค้าจากซัพพลายเออร์
                        </p>
                    </div>
                    <button
                        @click="handleExport"
                        :disabled="exporting"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-50 transition"
                    >
                        <svg v-if="exporting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg v-else class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        {{ exporting ? 'กำลัง Export...' : 'Export Excel' }}
                    </button>
                </div>

                <!-- Filters -->
                <div class="mb-6">
                    <PurchaseReportFilters
                        :suppliers="suppliers"
                        :filters="filters"
                        @filter="handleFilter"
                    />
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm font-medium text-gray-500">จำนวนใบสั่งซื้อ</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ summary.total_pos }}
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm font-medium text-gray-500">ยอดรวม</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ formatCurrency(summary.total_subtotal) }}
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm font-medium text-gray-500">VAT รวม</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ formatCurrency(summary.total_vat) }}
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm font-medium text-gray-500">ยอดรวมสุทธิ</div>
                        <div class="mt-1 text-3xl font-semibold text-green-600">
                            {{ formatCurrency(summary.grand_total) }}
                        </div>
                    </div>
                </div>

                <!-- Report Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <ReportTable
                        :data="purchaseOrders"
                        :columns="columns"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
