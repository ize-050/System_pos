<script setup>
import { computed } from 'vue';
import { Line } from 'vue-chartjs';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
);

const props = defineProps({
    chartData: {
        type: Object,
        required: true,
    },
    period: {
        type: Number,
        default: 30,
    },
});

const emit = defineEmits(['update:period']);

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top',
        },
        title: {
            display: true,
            text: 'ยอดขาย vs ยอดซื้อ',
        },
        tooltip: {
            callbacks: {
                label: function(context) {
                    let label = context.dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    label += new Intl.NumberFormat('th-TH', {
                        style: 'currency',
                        currency: 'THB',
                    }).format(context.parsed.y);
                    return label;
                }
            }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: function(value) {
                    return '฿' + value.toLocaleString('th-TH');
                }
            }
        }
    }
};

const data = computed(() => ({
    labels: props.chartData.dates || [],
    datasets: [
        {
            label: 'ยอดขาย',
            backgroundColor: 'rgba(34, 197, 94, 0.2)',
            borderColor: 'rgb(34, 197, 94)',
            data: props.chartData.sales || [],
            tension: 0.4,
        },
        {
            label: 'ยอดซื้อ',
            backgroundColor: 'rgba(239, 68, 68, 0.2)',
            borderColor: 'rgb(239, 68, 68)',
            data: props.chartData.purchases || [],
            tension: 0.4,
        },
    ],
}));

const periodOptions = [
    { value: 7, label: '7 วัน' },
    { value: 30, label: '30 วัน' },
    { value: 90, label: '90 วัน' },
];

const handlePeriodChange = (value) => {
    emit('update:period', value);
};
</script>

<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">กราฟเปรียบเทียบยอดขาย-ซื้อ</h3>
            <div class="flex space-x-2">
                <button
                    v-for="option in periodOptions"
                    :key="option.value"
                    @click="handlePeriodChange(option.value)"
                    :class="[
                        'px-3 py-1 text-sm rounded-md transition',
                        period === option.value
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                    ]"
                >
                    {{ option.label }}
                </button>
            </div>
        </div>
        <div class="h-80">
            <Line :data="data" :options="chartOptions" />
        </div>
    </div>
</template>
