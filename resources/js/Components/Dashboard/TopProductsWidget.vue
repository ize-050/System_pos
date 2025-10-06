<script setup>
const props = defineProps({
    products: {
        type: Array,
        default: () => [],
    },
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('th-TH', {
        style: 'currency',
        currency: 'THB',
    }).format(value);
};
</script>

<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">สินค้าขายดี Top 10</h3>
        <div class="space-y-3">
            <div
                v-for="(product, index) in products"
                :key="product.product_id"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition"
            >
                <div class="flex items-center space-x-3 flex-1">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                            <span class="text-sm font-bold text-indigo-600">{{ index + 1 }}</span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            {{ product.name }}
                        </p>
                        <p class="text-xs text-gray-500">
                            ขายได้ {{ product.total_quantity }} ชิ้น
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-green-600">
                        {{ formatCurrency(product.total_revenue) }}
                    </p>
                    <p class="text-xs text-gray-500">
                        คงเหลือ {{ product.current_stock }}
                    </p>
                </div>
            </div>
            <div v-if="!products || products.length === 0" class="text-center py-8 text-gray-500">
                ไม่มีข้อมูล
            </div>
        </div>
    </div>
</template>
