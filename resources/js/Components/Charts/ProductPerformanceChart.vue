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
    topProducts: {
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
    }
})

const chartCanvas = ref(null)
let chartInstance = null

const createChart = () => {
    if (!chartCanvas.value || !props.topProducts.length) return

    const ctx = chartCanvas.value.getContext('2d')
    
    // Destroy existing chart
    if (chartInstance) {
        chartInstance.destroy()
    }

    const labels = props.topProducts.map(product => product.name)
    const salesData = props.topProducts.map(product => product.total_sold)
    const revenueData = props.topProducts.map(product => product.total_revenue)
    const profitData = props.topProducts.map(product => product.gross_profit)

    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'จำนวนขาย',
                    data: salesData,
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1,
                    yAxisID: 'y'
                },
                {
                    label: 'รายได้',
                    data: revenueData,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1,
                    yAxisID: 'y1'
                },
                {
                    label: 'กำไร',
                    data: profitData,
                    backgroundColor: 'rgba(245, 158, 11, 0.8)',
                    borderColor: 'rgb(245, 158, 11)',
                    borderWidth: 1,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'ประสิทธิภาพสินค้า (Top 10)'
                },
                legend: {
                    position: 'top'
                }
            },
            scales: {
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'จำนวนขาย'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'บาท (THB)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
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
            }
        }
    })
}

watch(() => props.topProducts, () => {
    nextTick(() => {
        createChart()
    })
}, { deep: true })

onMounted(() => {
    nextTick(() => {
        createChart()
    })
})
</script>