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
    analysisType: {
        type: String,
        default: 'daily'
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

const formatCurrency = (value) => {
    return new Intl.NumberFormat('th-TH', {
        style: 'currency',
        currency: 'THB',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value)
}

const getDateLabel = (dateStr) => {
    const date = new Date(dateStr)
    
    switch (props.analysisType) {
        case 'daily':
            return date.toLocaleDateString('th-TH', { day: '2-digit', month: 'short' })
        case 'weekly':
            return `สัปดาห์ ${Math.ceil(date.getDate() / 7)}`
        case 'monthly':
            return date.toLocaleDateString('th-TH', { month: 'short', year: 'numeric' })
        case 'yearly':
            return date.getFullYear().toString()
        default:
            return dateStr
    }
}

const createChart = () => {
    if (!chartCanvas.value || !props.data.length) return

    const ctx = chartCanvas.value.getContext('2d')
    
    // Destroy existing chart
    if (chartInstance) {
        chartInstance.destroy()
    }

    const labels = props.data.map(item => getDateLabel(item.date))
    const salesData = props.data.map(item => item.total_sales || 0)
    const profitData = props.data.map(item => item.gross_profit || 0)
    const costData = props.data.map(item => (item.total_sales || 0) - (item.gross_profit || 0))

    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'ยอดขาย',
                    data: salesData,
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1,
                    order: 2
                },
                {
                    label: 'ต้นทุน',
                    data: costData,
                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    borderColor: 'rgb(239, 68, 68)',
                    borderWidth: 1,
                    order: 3
                },
                {
                    label: 'กำไรขั้นต้น',
                    data: profitData,
                    type: 'line',
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 3,
                    fill: false,
                    tension: 0.4,
                    order: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                title: {
                    display: true,
                    text: 'การวิเคราะห์กำไรขั้นต้น'
                },
                legend: {
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.dataset.label || ''
                            const value = formatCurrency(context.parsed.y)
                            return `${label}: ${value}`
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'ช่วงเวลา'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'จำนวนเงิน (บาท)'
                    },
                    ticks: {
                        callback: function(value) {
                            return formatCurrency(value)
                        }
                    }
                }
            }
        }
    })
}

watch(() => [props.data, props.analysisType], () => {
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