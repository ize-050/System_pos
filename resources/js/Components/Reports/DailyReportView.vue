<script setup>
import ExportButton from './ExportButton.vue';

const props = defineProps({
    report: {
        type: Object,
        required: true,
    },
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('th-TH', {
        style: 'currency',
        currency: 'THB',
    }).format(value);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const profitMargin = ((props.report.profit_loss / props.report.sales_total) * 100).toFixed(2);
</script>

<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-xl font-bold text-gray-900">รายงานประจำวัน</h3>
                <p class="text-sm text-gray-600 mt-1">{{ formatDate(report.report_date) }}</p>
            </div>
            <ExportButton
                report-type="daily_report"
                :filters="{ report_date: report.report_date }"
            />
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Sales Total -->
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">ยอดขายรวม</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">
                            {{ formatCurrency(report.sales_total) }}
                        </p>
                    </div>
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Purchase Total -->
            <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">ยอดซื้อรวม</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">
                            {{ formatCurrency(report.purchase_total) }}
                        </p>
                    </div>
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>

            <!-- Profit/Loss -->
            <div :class="[
                'rounded-lg p-4 border-2',
                report.profit_loss >= 0 ? 'bg-indigo-50 border-indigo-300' : 'bg-orange-50 border-orange-300'
            ]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">กำไร/ขาดทุน</p>
                        <p :class="[
                            'text-2xl font-bold mt-1',
                            report.profit_loss >= 0 ? 'text-indigo-600' : 'text-orange-600'
                        ]">
                            {{ formatCurrency(report.profit_loss) }}
                        </p>
                    </div>
                    <svg class="w-10 h-10" :class="report.profit_loss >= 0 ? 'text-indigo-600' : 'text-orange-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>

            <!-- Transactions -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">จำนวนธุรกรรม</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            {{ report.transaction_count }}
                        </p>
                    </div>
                    <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Profit Margin -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">อัตรากำไร</h4>
                <div class="flex items-center space-x-3">
                    <div class="flex-1">
                        <div class="relative pt-1">
                            <div class="flex mb-2 items-center justify-between">
                                <div>
                                    <span :class="[
                                        'text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full',
                                        profitMargin >= 30 ? 'text-green-600 bg-green-200' :
                                        profitMargin >= 15 ? 'text-yellow-600 bg-yellow-200' :
                                        'text-red-600 bg-red-200'
                                    ]">
                                        {{ profitMargin }}%
                                    </span>
                                </div>
                            </div>
                            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-200">
                                <div
                                    :style="{ width: Math.min(profitMargin, 100) + '%' }"
                                    :class="[
                                        'shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center',
                                        profitMargin >= 30 ? 'bg-green-500' :
                                        profitMargin >= 15 ? 'bg-yellow-500' :
                                        'bg-red-500'
                                    ]"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Generated Info -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">ข้อมูลการสร้างรายงาน</h4>
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>สร้างเมื่อ:</span>
                        <span class="font-medium">{{ new Date(report.generated_at).toLocaleString('th-TH') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>อัปเดตล่าสุด:</span>
                        <span class="font-medium">{{ new Date(report.updated_at).toLocaleString('th-TH') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
