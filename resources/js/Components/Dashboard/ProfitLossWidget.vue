<script setup>
import { computed } from 'vue';

const props = defineProps({
    data: {
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

const profitMarginColor = computed(() => {
    const margin = props.data.profit_margin_percentage || 0;
    if (margin >= 30) return 'text-green-600';
    if (margin >= 15) return 'text-yellow-600';
    return 'text-red-600';
});
</script>

<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">สรุปกำไร-ขาดทุน</h3>
        
        <div class="space-y-4">
            <!-- Total Revenue -->
            <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                <div>
                    <p class="text-sm text-gray-600">รายได้รวม</p>
                    <p class="text-xs text-gray-500 mt-1">Total Revenue</p>
                </div>
                <p class="text-xl font-bold text-green-600">
                    {{ formatCurrency(data.total_revenue || 0) }}
                </p>
            </div>

            <!-- Total COGS -->
            <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                <div>
                    <p class="text-sm text-gray-600">ต้นทุนขาย</p>
                    <p class="text-xs text-gray-500 mt-1">Cost of Goods Sold</p>
                </div>
                <p class="text-xl font-bold text-red-600">
                    {{ formatCurrency(data.total_cogs || 0) }}
                </p>
            </div>

            <!-- Gross Profit -->
            <div class="flex justify-between items-center p-3 bg-indigo-50 rounded-lg border-2 border-indigo-200">
                <div>
                    <p class="text-sm font-semibold text-gray-900">กำไรขั้นต้น</p>
                    <p class="text-xs text-gray-500 mt-1">Gross Profit</p>
                </div>
                <p class="text-2xl font-bold text-indigo-600">
                    {{ formatCurrency(data.gross_profit || 0) }}
                </p>
            </div>

            <!-- Profit Margin -->
            <div class="mt-4 p-4 bg-gray-50 rounded-lg text-center">
                <p class="text-sm text-gray-600 mb-2">อัตรากำไร</p>
                <div class="flex items-center justify-center space-x-2">
                    <svg class="w-6 h-6" :class="profitMarginColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    <span class="text-3xl font-bold" :class="profitMarginColor">
                        {{ (data.profit_margin_percentage || 0).toFixed(2) }}%
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    <span v-if="data.profit_margin_percentage >= 30" class="text-green-600">● ดีมาก</span>
                    <span v-else-if="data.profit_margin_percentage >= 15" class="text-yellow-600">● ปานกลาง</span>
                    <span v-else class="text-red-600">● ต้องปรับปรุง</span>
                </p>
            </div>
        </div>
    </div>
</template>
