<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    data: Object,
    columns: Array,
});

const sortKey = ref('');
const sortOrder = ref('asc');

const sortedData = computed(() => {
    if (!sortKey.value) return props.data.data;

    return [...props.data.data].sort((a, b) => {
        const aVal = getNestedValue(a, sortKey.value);
        const bVal = getNestedValue(b, sortKey.value);

        if (aVal < bVal) return sortOrder.value === 'asc' ? -1 : 1;
        if (aVal > bVal) return sortOrder.value === 'asc' ? 1 : -1;
        return 0;
    });
});

const getNestedValue = (obj, path) => {
    return path.split('.').reduce((acc, part) => acc?.[part], obj);
};

const handleSort = (key) => {
    if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortKey.value = key;
        sortOrder.value = 'asc';
    }
};

const formatValue = (row, column) => {
    const value = getNestedValue(row, column.key);

    if (column.format === 'currency') {
        return formatCurrency(value);
    } else if (column.format === 'status') {
        return formatStatus(value);
    } else if (column.format === 'date') {
        return formatDate(value);
    }

    return value;
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('th-TH', {
        style: 'currency',
        currency: 'THB',
    }).format(value || 0);
};

const formatStatus = (status) => {
    const statusMap = {
        'draft': 'ฉบับร่าง',
        'pending': 'รอดำเนินการ',
        'shipping': 'กำลังจัดส่ง',
        'received': 'รับสินค้าแล้ว',
        'cancelled': 'ยกเลิก',
    };
    return statusMap[status] || status;
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('th-TH');
};

const getStatusClass = (status) => {
    const statusClasses = {
        'draft': 'bg-gray-100 text-gray-800',
        'pending': 'bg-yellow-100 text-yellow-800',
        'shipping': 'bg-blue-100 text-blue-800',
        'received': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800',
    };
    return statusClasses[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <div>
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            v-for="column in columns"
                            :key="column.key"
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            :class="{ 'cursor-pointer hover:bg-gray-100': column.sortable }"
                            @click="column.sortable ? handleSort(column.key) : null"
                        >
                            <div class="flex items-center space-x-1">
                                <span>{{ column.label }}</span>
                                <svg
                                    v-if="column.sortable && sortKey === column.key"
                                    class="w-4 h-4"
                                    :class="sortOrder === 'asc' ? 'transform rotate-180' : ''"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr
                        v-for="row in sortedData"
                        :key="row.id"
                        class="hover:bg-gray-50"
                    >
                        <td
                            v-for="column in columns"
                            :key="column.key"
                            class="px-6 py-4 whitespace-nowrap text-sm"
                        >
                            <span
                                v-if="column.format === 'status'"
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                :class="getStatusClass(getNestedValue(row, column.key))"
                            >
                                {{ formatValue(row, column) }}
                            </span>
                            <span v-else class="text-gray-900">
                                {{ formatValue(row, column) }}
                            </span>
                        </td>
                    </tr>
                    <tr v-if="!sortedData || sortedData.length === 0">
                        <td
                            :colspan="columns.length"
                            class="px-6 py-4 text-center text-sm text-gray-500"
                        >
                            ไม่พบข้อมูล
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="data.links && data.links.length > 3" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <Link
                    v-if="data.prev_page_url"
                    :href="data.prev_page_url"
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                    ก่อนหน้า
                </Link>
                <Link
                    v-if="data.next_page_url"
                    :href="data.next_page_url"
                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                    ถัดไป
                </Link>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        แสดง
                        <span class="font-medium">{{ data.from }}</span>
                        ถึง
                        <span class="font-medium">{{ data.to }}</span>
                        จาก
                        <span class="font-medium">{{ data.total }}</span>
                        รายการ
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <Link
                            v-for="(link, index) in data.links"
                            :key="index"
                            :href="link.url"
                            :class="[
                                link.active
                                    ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                index === 0 ? 'rounded-l-md' : '',
                                index === data.links.length - 1 ? 'rounded-r-md' : '',
                            ]"
                            v-html="link.label"
                        />
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>
