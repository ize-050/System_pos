<template>
    <div class="relative">
        <canvas ref="chartCanvas" :width="width" :height="height"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick } from 'vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const props = defineProps({
    regularCustomers: {
        type: Number,
        default: 0
    },
    newCustomers: {
        type: Number,
        default: 0
    },
    width: {
        type: Number,
        default: 400
    },
    height: {
        type: Number,
        default: 200
    }
})

const chartCanvas = ref(null)
let chartInstance = null

const createChart = () => {
    if (!chartCanvas.value) return

    const ctx = chartCanvas.value.getContext('2d')
    
    // Destroy existing chart
    if (chartInstance) {
        chartInstance.destroy()
    }

    const totalCustomers = props.regularCustomers + props.newCustomers
    if (totalCustomers === 0) return

    chartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['ลูกค้าประจำ', 'ลูกค้าใหม่'],
            datasets: [{
                data: [props.regularCustomers, props.newCustomers],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',  // Regular - Blue
                    'rgba(16, 185, 129, 0.8)'   // New - Green
                ],
                borderColor: [
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'การแบ่งกลุ่มลูกค้า'
                },
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || ''
                            const value = context.parsed
                            const percentage = ((value / totalCustomers) * 100).toFixed(1)
                            return `${label}: ${value} คน (${percentage}%)`
                        }
                    }
                }
            }
        }
    })
}

watch(() => [props.regularCustomers, props.newCustomers], () => {
    nextTick(() => {
        createChart()
    })
})

onMounted(() => {
    nextTick(() => {
        createChart()
    })
})
</script>