<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    suppliers: Array,
    filters: Object,
});

const emit = defineEmits(['filter']);

const form = ref({
    start_date: props.filters?.start_date || '',
    end_date: props.filters?.end_date || '',
    supplier_id: props.filters?.supplier_id || '',
    status: props.filters?.status || '',
    per_page: props.filters?.per_page || 50,
});

const statusOptions = [
    { value: '', label: 'ทั้งหมด' },
    { value: 'draft', label: 'ฉบับร่าง' },
    { value: 'pending', label: 'รอดำเนินการ' },
    { value: 'shipping', label: 'กำลังจัดส่ง' },
    { value: 'received', label: 'รับสินค้าแล้ว' },
    { value: 'cancelled', label: 'ยกเลิก' },
];

const perPageOptions = [25, 50, 100];

const handleFilter = () => {
    emit('filter', form.value);
};

const handleReset = () => {
    form.value = {
        start_date: '',
        end_date: '',
        supplier_id: '',
        status: '',
        per_page: 50,
    };
    handleFilter();
};
</script>

<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Start Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    วันที่เริ่มต้น
                </label>
                <input
                    v-model="form.start_date"
                    type="date"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
            </div>

            <!-- End Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    วันที่สิ้นสุด
                </label>
                <input
                    v-model="form.end_date"
                    type="date"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
            </div>

            <!-- Supplier -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    ซัพพลายเออร์
                </label>
                <select
                    v-model="form.supplier_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
                    <option value="">ทั้งหมด</option>
                    <option
                        v-for="supplier in suppliers"
                        :key="supplier.id"
                        :value="supplier.id"
                    >
                        {{ supplier.name }}
                    </option>
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    สถานะ
                </label>
                <select
                    v-model="form.status"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
                    <option
                        v-for="option in statusOptions"
                        :key="option.value"
                        :value="option.value"
                    >
                        {{ option.label }}
                    </option>
                </select>
            </div>

            <!-- Per Page -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    แสดงต่อหน้า
                </label>
                <select
                    v-model="form.per_page"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
                    <option
                        v-for="option in perPageOptions"
                        :key="option"
                        :value="option"
                    >
                        {{ option }} รายการ
                    </option>
                </select>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-4 flex justify-end space-x-3">
            <button
                @click="handleReset"
                type="button"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
            >
                ล้างตัวกรอง
            </button>
            <button
                @click="handleFilter"
                type="button"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
            >
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                กรองข้อมูล
            </button>
        </div>
    </div>
</template>
