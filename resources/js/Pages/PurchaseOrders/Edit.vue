<template>
  <AppLayout title="แก้ไขใบสั่งซื้อ">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">แก้ไขใบสั่งซื้อ</h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
          <h2 class="text-2xl font-bold mb-6">แก้ไขใบสั่งซื้อ: {{ purchaseOrder.po_number }}</h2>
          <form @submit.prevent="submit">
            <!-- Supplier Selection -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700">ซัพพลายเออร์ *</label>
              <select v-model="form.supplier_id" required class="mt-1 block w-full rounded-md border-gray-300">
                <option value="">เลือกซัพพลายเออร์</option>
                <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                  {{ supplier.name }}
                </option>
              </select>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">วันที่สั่งซื้อ *</label>
                <input v-model="form.order_date" type="date" required class="mt-1 block w-full rounded-md border-gray-300" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">วันที่คาดว่าจะได้รับ</label>
                <input v-model="form.expected_delivery_date" type="date" class="mt-1 block w-full rounded-md border-gray-300" />
              </div>
            </div>

            <!-- Items -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Items *</label>
              <div v-for="(item, index) in form.items" :key="index" class="flex gap-2 mb-2">
                <select v-model="item.product_id" @change="selectProduct(index)" class="flex-1 rounded-md border-gray-300">
                  <option value="">Select Product</option>
                  <option v-for="product in products" :key="product.id" :value="product.id">
                    {{ product.name }} ({{ product.sku }})
                  </option>
                </select>
                <input v-model.number="item.quantity_ordered" type="number" step="0.01" placeholder="Qty" class="w-24 rounded-md border-gray-300" />
                <input v-model.number="item.unit_price" type="number" step="0.01" placeholder="Price" class="w-32 rounded-md border-gray-300" />
                <span class="py-2">{{ formatCurrency(item.quantity_ordered * item.unit_price) }}</span>
                <button type="button" @click="removeItem(index)" class="text-red-600">Remove</button>
              </div>
              <button type="button" @click="addItem" class="mt-2 text-indigo-600">+ Add Item</button>
            </div>

            <!-- Totals -->
            <div class="border-t pt-4 mb-4">
              <div class="flex justify-end gap-4 text-sm">
                <div class="text-right space-y-2">
                  <div class="flex justify-between gap-4">
                    <span>Subtotal:</span>
                    <span>{{ formatCurrency(subtotal) }}</span>
                  </div>
                  <div class="flex justify-between gap-4 items-center">
                    <span>Discount:</span>
                    <input v-model.number="form.discount_amount" type="number" step="0.01" min="0" class="w-32 rounded-md border-gray-300 text-sm text-right" />
                  </div>
                  <div class="flex justify-between gap-4 items-center">
                    <label class="flex items-center gap-2">
                      <input v-model="form.include_vat" type="checkbox" class="rounded border-gray-300" />
                      <span>Include VAT 7%:</span>
                    </label>
                    <span>{{ form.include_vat ? formatCurrency(vatAmount) : formatCurrency(0) }}</span>
                  </div>
                  <div class="flex justify-between gap-4 text-lg font-bold border-t pt-2">
                    <span>Total:</span>
                    <span>{{ formatCurrency(totalAmount) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Notes -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700">Notes</label>
              <textarea v-model="form.notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300"></textarea>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-2">
              <Link :href="route('purchase-orders.show', purchaseOrder.id)" class="px-4 py-2 bg-gray-300 rounded-md">ยกเลิก</Link>
              <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-indigo-600 text-white rounded-md disabled:opacity-50">บันทึกการแก้ไข</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { reactive, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  purchaseOrder: Object,
  suppliers: Array,
  products: Array,
});

const form = useForm({
  supplier_id: props.purchaseOrder.supplier_id,
  order_date: props.purchaseOrder.order_date,
  expected_delivery_date: props.purchaseOrder.expected_delivery_date,
  items: props.purchaseOrder.items.map(item => ({
    product_id: item.product_id,
    product_name: item.product_name,
    quantity_ordered: item.quantity_ordered,
    unit_price: item.unit_price
  })),
  discount_amount: props.purchaseOrder.discount_amount || 0,
  include_vat: props.purchaseOrder.vat_amount > 0,
  notes: props.purchaseOrder.notes || '',
});

const addItem = () => {
  form.items.push({ product_id: '', product_name: '', quantity_ordered: 1, unit_price: 0 });
};

const removeItem = (index) => {
  form.items.splice(index, 1);
};

const selectProduct = (index) => {
  const product = props.products.find(p => p.id === form.items[index].product_id);
  if (product) {
    form.items[index].product_name = product.name;
    form.items[index].unit_price = product.cost_price || 0;
  }
};

const subtotal = computed(() => {
  return form.items.reduce((sum, item) => sum + (item.quantity_ordered * item.unit_price), 0);
});

const vatAmount = computed(() => {
  if (!form.include_vat) return 0;
  return (subtotal.value - form.discount_amount) * 0.07;
});

const totalAmount = computed(() => {
  return subtotal.value - form.discount_amount + vatAmount.value;
});

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('th-TH', { style: 'currency', currency: 'THB' }).format(amount || 0);
};

const submit = () => {
  form.put(route('purchase-orders.update', props.purchaseOrder.id));
};
</script>
