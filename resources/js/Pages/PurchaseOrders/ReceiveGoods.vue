<template>
  <AppLayout title="รับสินค้า">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">รับสินค้า</h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg p-6">
          <h2 class="text-2xl font-bold mb-6">รับสินค้า - {{ purchaseOrder.po_number }}</h2>
          <form @submit.prevent="submit">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700">วันที่รับสินค้า *</label>
              <input v-model="form.received_date" type="date" required class="mt-1 block w-full rounded-md border-gray-300" />
            </div>

            <table class="min-w-full divide-y divide-gray-200 mb-6">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-2 text-left">สินค้า</th>
                  <th class="px-4 py-2 text-right">จำนวนสั่ง</th>
                  <th class="px-4 py-2 text-right">จำนวนรับ</th>
                  <th class="px-4 py-2">สภาพ</th>
                  <th class="px-4 py-2">หมายเหตุ</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in form.items" :key="item.id">
                  <td class="px-4 py-2">{{ item.product_name }}</td>
                  <td class="px-4 py-2 text-right">{{ item.quantity_ordered }}</td>
                  <td class="px-4 py-2">
                    <input v-model.number="item.quantity_received" type="number" step="0.01" required class="w-24 rounded-md border-gray-300 text-right" />
                  </td>
                  <td class="px-4 py-2">
                    <select v-model="item.condition_status" required class="rounded-md border-gray-300">
                      <option value="good">สภาพดี</option>
                      <option value="partial_damaged">ชำรุดบ้าง</option>
                      <option value="fully_damaged">ชำรุดทั้งหมด</option>
                    </select>
                  </td>
                  <td class="px-4 py-2">
                    <input v-model="item.notes" type="text" class="w-full rounded-md border-gray-300" />
                  </td>
                </tr>
              </tbody>
            </table>

            <div class="flex justify-end gap-2">
              <Link :href="route('purchase-orders.show', purchaseOrder.id)" class="px-4 py-2 bg-gray-300 rounded-md">ยกเลิก</Link>
              <button 
                type="submit" 
                :disabled="form.processing" 
                class="px-4 py-2 bg-green-600 text-white rounded-md disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              >
                <svg v-if="form.processing" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ form.processing ? 'กำลังประมวลผล...' : 'ยืนยันการรับสินค้า' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  purchaseOrder: Object,
});

const form = useForm({
  received_date: new Date().toISOString().split('T')[0],
  items: props.purchaseOrder.items.map(item => ({
    id: item.id,
    product_name: item.product_name,
    quantity_ordered: item.quantity_ordered,
    quantity_received: item.quantity_ordered,
    condition_status: 'good',
    notes: '',
  })),
});

const submit = () => {
  form.post(route('purchase-orders.receive.store', props.purchaseOrder.id));
};
</script>
