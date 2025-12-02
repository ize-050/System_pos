<template>
    <div class="receipt-container" :class="containerClass">
        <!-- Use Template based on printer_type -->
        <A4Template v-if="settings?.printer_type === 'a4'" :sale="sale" :settings="settings" :qrCode="null" :barcode="null" />
        <DotMatrixTemplate v-else-if="settings?.printer_type === 'dot_matrix'" :sale="sale" :settings="settings" />
        <ThermalTemplate v-else :sale="sale" :settings="settings" :qrCode="null" :barcode="null" />
        
        <!-- Print Button -->
        <div class="print-actions no-print">
            <button @click="printReceipt" class="print-btn">พิมพ์ใบเสร็จ</button>
            <button @click="closeWindow" class="close-btn">ปิด</button>
        </div>
    </div>
</template>

<script setup>
import { onMounted, computed } from 'vue'
import A4Template from '../Receipts/Templates/A4Template.vue'
import ThermalTemplate from '../Receipts/Templates/ThermalTemplate.vue'
import DotMatrixTemplate from '../Receipts/Templates/DotMatrixTemplate.vue'

const props = defineProps({
    sale: Object,
    settings: Object
})

// Compute container class based on printer type
const containerClass = computed(() => {
    if (props.settings?.printer_type === 'a4') {
        return ''
    } else if (props.settings?.printer_type === 'dot_matrix') {
        return 'dot-matrix-container'
    } else {
        return 'thermal-container'
    }
})

const printReceipt = () => {
    window.print()
}

const closeWindow = () => {
    window.close()
}

onMounted(() => {
    // Auto print after page loads
    setTimeout(() => {
        window.print()
    }, 1000)
})
</script>

<style scoped>
.receipt-container {
    padding: 20px;
}

.thermal-container {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
    background: #f5f5f5;
}

.dot-matrix-container {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
    background: #f0f0f0;
    overflow-x: auto;
    padding: 20px;
}

.print-actions {
    text-align: center;
    margin-top: 20px;
    gap: 10px;
    display: flex;
    justify-content: center;
}

.print-btn, .close-btn {
    padding: 10px 20px;
    border: 1px solid #333;
    background: #fff;
    cursor: pointer;
    font-size: 14px;
    border-radius: 4px;
    transition: all 0.3s;
}

.print-btn {
    background: #4CAF50;
    color: white;
    border-color: #4CAF50;
}

.print-btn:hover {
    background: #45a049;
}

.close-btn:hover {
    background: #f0f0f0;
}

@media print {
    .no-print {
        display: none !important;
    }
}
</style>