<template>
  <AppLayout title="Purchase Orders">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">ใบสั่งซื้อสินค้า (PO)</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
          <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
            <div class="text-gray-500 text-sm">ฉบับร่าง</div>
            <div class="text-2xl font-bold">{{ statistics.draft }}</div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
            <div class="text-gray-500 text-sm">รอดำเนินการ</div>
            <div class="text-2xl font-bold text-yellow-600">{{ statistics.pending }}</div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
            <div class="text-gray-500 text-sm">กำลังจัดส่ง</div>
            <div class="text-2xl font-bold text-blue-600">{{ statistics.shipping }}</div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
            <div class="text-gray-500 text-sm">รับสินค้าแล้ว</div>
            <div class="text-2xl font-bold text-green-600">{{ statistics.received }}</div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
            <div class="text-gray-500 text-sm">ยกเลิก</div>
            <div class="text-2xl font-bold text-red-600">{{ statistics.cancelled }}</div>
          </div>
        </div>

        <!-- Filters and Actions -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6 p-6">
          <div class="flex flex-wrap gap-4 items-center justify-between">
            <div class="flex gap-4 flex-1">
              <input
                v-model="form.search"
                type="text"
                placeholder="ค้นหาเลขที่ PO หรือซัพพลายเออร์..."
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                @input="search"
              />
              <select
                v-model="form.status"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                @change="filter"
              >
                <option value="all">ทุกสถานะ</option>
                <option value="draft">ฉบับร่าง</option>
                <option value="pending">รอดำเนินการ</option>
                <option value="shipping">กำลังจัดส่ง</option>
                <option value="received">รับสินค้าแล้ว</option>
                <option value="cancelled">ยกเลิก</option>
              </select>
              <select
                v-model="form.supplier_id"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                @change="filter"
              >
                <option value="">ซัพพลายเออร์ทั้งหมด</option>
                <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                  {{ supplier.name }}
                </option>
              </select>
            </div>
            <div class="flex gap-2">
              <button
                @click="exportExcel"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
              >
                ส่งออก Excel
              </button>
              <Link
                :href="route('purchase-orders.create')"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
              >
                + สร้างใบสั่งซื้อ
              </Link>
            </div>
          </div>
        </div>

        <!-- Table -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">เลขที่ PO</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ซัพพลายเออร์</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">วันที่สั่ง</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ยอดรวม</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">สถานะ</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">จัดการ</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="po in purchaseOrders.data" :key="po.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ po.po_number }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ po.supplier?.name || '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(po.order_date) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatCurrency(po.total_amount) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStatusClass(po.status)" class="px-2 py-1 text-xs rounded-full">
                    {{ po.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <Link
                    :href="route('purchase-orders.show', po.id)"
                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                  >
                    ดูรายละเอียด
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Pagination -->
          <div v-if="purchaseOrders?.links && purchaseOrders.links.length > 0" class="px-6 py-4 border-t">
            <div class="flex justify-between items-center">
              <div class="text-sm text-gray-700">
                แสดง {{ purchaseOrders.from || 0 }} ถึง {{ purchaseOrders.to || 0 }} จากทั้งหมด {{ purchaseOrders.total || 0 }} รายการ
              </div>
              <div class="flex gap-2">
                <component
                  :is="link.url ? Link : 'span'"
                  v-for="(link, index) in purchaseOrders.links"
                  :key="index"
                  :href="link.url || undefined"
                  :class="[
                    'px-3 py-2 rounded-md text-sm',
                    link.active ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700',
                    link.url ? 'hover:bg-gray-50 cursor-pointer' : 'opacity-50 cursor-not-allowed'
                  ]"
                  v-html="link.label"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  purchaseOrders: Object,
  statistics: Object,
  suppliers: Array,
  filters: Object,
});

const form = reactive({
  search: props.filters.search || '',
  status: props.filters.status || 'all',
  supplier_id: props.filters.supplier_id || '',
});

const search = () => {
  router.get(route('purchase-orders.index'), form, {
    preserveState: true,
    replace: true,
  });
};

const filter = () => {
  router.get(route('purchase-orders.index'), form, {
    preserveState: true,
    replace: true,
  });
};

const exportExcel = () => {
  window.location.href = route('purchase-orders.export');
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('th-TH');
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('th-TH', {
    style: 'currency',
    currency: 'THB',
  }).format(amount);
};

const getStatusClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    pending: 'bg-yellow-100 text-yellow-800',
    shipping: 'bg-blue-100 text-blue-800',
    received: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};
</script>
