<script setup>
const props = defineProps({
    products: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">แจ้งเตือนสต็อกต่ำ</h3>
            <span class="px-2 py-1 text-xs font-semibold text-red-600 bg-red-100 rounded-full">
                {{ products.length }} รายการ
            </span>
        </div>
        <div class="space-y-3 max-h-96 overflow-y-auto">
            <div
                v-for="product in products"
                :key="product.id"
                class="flex items-center justify-between p-3 border border-red-200 bg-red-50 rounded-lg"
            >
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">
                        {{ product.name }}
                    </p>
                    <p class="text-xs text-gray-600 mt-1">
                        SKU: {{ product.sku }}
                    </p>
                    <div class="flex items-center mt-2 space-x-2">
                        <span class="text-xs text-red-600 font-semibold">
                            คงเหลือ: {{ product.current_stock }}
                        </span>
                        <span class="text-xs text-gray-500">
                            ขั้นต่ำ: {{ product.minimum_stock }}
                        </span>
                    </div>
                </div>
                <div class="text-right ml-4">
                    <div class="text-xs text-gray-600 mb-1">แนะนำสั่งซื้อ</div>
                    <div class="text-lg font-bold text-indigo-600">
                        {{ product.reorder_quantity || (product.minimum_stock * 2 - product.current_stock) }}
                    </div>
                    <div class="text-xs text-gray-500">ชิ้น</div>
                </div>
            </div>
            <div v-if="!products || products.length === 0" class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="mt-2 text-sm text-gray-500">สต็อกปกติทั้งหมด</p>
            </div>
        </div>
    </div>
</template>
