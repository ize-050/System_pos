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
    data: {
        type: Array,
        default: () => []
    },
    width: {
        type: Number,
        default: 400
    },
    height: {
        type: Number,
        default: 200
    },
    analysisType: {
        type: String,
        default: 'daily'
    }
})

const chartCanvas = ref(null)
let chartInstance = null

const createChart = () => {
    if (!chartCanvas.value || !props.data.length) return

    const ctx = chartCanvas.value.getContext('2d')
    
    // Destroy existing chart
    if (chartInstance) {
        chartInstance.destroy()
    }

    const labels = props.data.map(item => {
        const date = new Date(item.date)
        switch (props.analysisType) {
            case 'daily':
                return date.toLocaleDateString('th-TH', { day: '2-digit', month: 'short' })
            case 'weekly':
                return `สัปดาห์ ${item.week}`
            case 'monthly':
                return date.toLocaleDateString('th-TH', { month: 'short', year: 'numeric' })
            case 'yearly':
                return date.getFullYear().toString()
            default:
                return item.date
        }
    })

    const salesData = props.data.map(item => item.total_sales)
    const profitData = props.data.map(item => item.gross_profit)

    chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'ยอดขาย',
                    data: salesData,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'กำไรขั้นต้น',
                    data: profitData,
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'แนวโน้มยอดขายและกำไร'
                },
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('th-TH', {
                                style: 'currency',
                                currency: 'THB',
                                minimumFractionDigits: 0
                            }).format(value)
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    })
}

watch(() => props.data, () => {
    nextTick(() => {
        createChart()
    })
}, { deep: true })

watch(() => props.analysisType, () => {
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