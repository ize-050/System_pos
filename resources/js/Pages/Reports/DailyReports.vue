<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    reports: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('th-TH', {
        style: 'currency',
        currency: 'THB',
    }).format(value);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const getProfitClass = (value) => {
    return value >= 0 ? 'text-green-600' : 'text-red-600';
};
</script>

<template>
    <AppLayout title="รายงานประจำวัน">
        <Head title="รายงานประจำวัน" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">รายงานประจำวัน</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        สรุปยอดขาย-ซื้อ และกำไร-ขาดทุนรายวัน
                    </p>
                </div>

                <!-- Reports List -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        วันที่
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ยอดขาย
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ยอดซื้อ
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        กำไร/ขาดทุน
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ธุรกรรม
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        การดำเนินการ
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="report in reports.data"
                                    :key="report.id"
                                    class="hover:bg-gray-50"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ formatDate(report.report_date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600 font-semibold">
                                        {{ formatCurrency(report.sales_total) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600 font-semibold">
                                        {{ formatCurrency(report.purchase_total) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold" :class="getProfitClass(report.profit_loss)">
                                        {{ formatCurrency(report.profit_loss) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                        {{ report.transaction_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <Link
                                            :href="route('reports.daily.show', report.report_date)"
                                            class="text-indigo-600 hover:text-indigo-900"
                                        >
                                            ดูรายละเอียด
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="!reports.data || reports.data.length === 0">
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        ไม่มีข้อมูล
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="reports.links && reports.links.length > 3" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link
                                v-if="reports.prev_page_url"
                                :href="reports.prev_page_url"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                ก่อนหน้า
                            </Link>
                            <Link
                                v-if="reports.next_page_url"
                                :href="reports.next_page_url"
                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                ถัดไป
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    แสดง
                                    <span class="font-medium">{{ reports.from }}</span>
                                    ถึง
                                    <span class="font-medium">{{ reports.to }}</span>
                                    จาก
                                    <span class="font-medium">{{ reports.total }}</span>
                                    รายการ
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <Link
                                        v-for="(link, index) in reports.links"
                                        :key="index"
                                        :href="link.url"
                                        :class="[
                                            link.active
                                                ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                                                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                            index === 0 ? 'rounded-l-md' : '',
                                            index === reports.links.length - 1 ? 'rounded-r-md' : '',
                                        ]"
                                        v-html="link.label"
                                    />
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
