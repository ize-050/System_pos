<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    reportType: {
        type: String,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    format: {
        type: String,
        default: 'xlsx',
    },
});

const exporting = ref(false);

const handleExport = async () => {
    exporting.value = true;
    
    try {
        const response = await axios.post(route('reports.export'), {
            report_type: props.reportType,
            format: props.format,
            filters: props.filters,
        });

        if (response.data.success) {
            // Download file
            window.location.href = response.data.download_url;
            
            // Show success message
            alert('Export สำเร็จ! กำลังดาวน์โหลดไฟล์...');
        }
    } catch (error) {
        console.error('Export error:', error);
        alert('เกิดข้อผิดพลาดในการ Export: ' + (error.response?.data?.message || error.message));
    } finally {
        exporting.value = false;
    }
};
</script>

<template>
    <button
        @click="handleExport"
        :disabled="exporting"
        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-50 transition"
    >
        <svg
            v-if="exporting"
            class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
        >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <svg
            v-else
            class="-ml-1 mr-2 h-4 w-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        {{ exporting ? 'กำลัง Export...' : 'Export Excel' }}
    </button>
</template>
