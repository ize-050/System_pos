<template>
  <div class="a4-receipt" style="width: 210mm; min-height: 297mm; padding: 20mm; font-family: 'Sarabun', sans-serif;">
    <!-- Header -->
    <div class="text-center mb-8">
      <img v-if="settings.show_logo && settings.logo_path" :src="`/storage/${settings.logo_path}`" class="h-20 mx-auto mb-4" />
      <h1 class="text-3xl font-bold">{{ settings.shop_name }}</h1>
      <p class="text-sm">{{ settings.shop_address }}</p>
      <p class="text-sm">Tel: {{ settings.shop_phone }} | Email: {{ settings.shop_email }}</p>
      <p v-if="settings.tax_id" class="text-sm">Tax ID: {{ settings.tax_id }}</p>
    </div>

    <!-- Receipt Info -->
    <div class="border-t border-b py-4 mb-4">
      <div class="grid grid-cols-2 gap-4 text-sm">
        <div><strong>Receipt No:</strong> {{ sale.sale_number }}</div>
        <div class="text-right"><strong>Date:</strong> {{ formatDate(sale.created_at) }}</div>
        <div v-if="settings.show_customer_info && sale.customer"><strong>Customer:</strong> {{ sale.customer.name }}</div>
        <div class="text-right"><strong>Cashier:</strong> {{ sale.cashier?.name }}</div>
      </div>
    </div>

    <!-- Items -->
    <table class="w-full mb-4">
      <thead class="border-b-2">
        <tr>
          <th class="text-left py-2">Item</th>
          <th class="text-center py-2">Qty</th>
          <th class="text-right py-2">Price</th>
          <th class="text-right py-2">Total</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in sale.sale_items" :key="item.id" class="border-b">
          <td class="py-2">{{ item.product?.name }}</td>
          <td class="text-center py-2">{{ item.quantity }}</td>
          <td class="text-right py-2">{{ formatCurrency(item.unit_price) }}</td>
          <td class="text-right py-2">{{ formatCurrency(item.total_price) }}</td>
        </tr>
      </tbody>
    </table>

    <!-- Totals -->
    <div class="flex justify-end mb-8">
      <div class="w-64">
        <div class="flex justify-between py-1"><span>Subtotal:</span><span>{{ formatCurrency(sale.subtotal) }}</span></div>
        <div v-if="settings.show_vat" class="flex justify-between py-1"><span>VAT 7%:</span><span>{{ formatCurrency(sale.vat_amount) }}</span></div>
        <div class="flex justify-between py-2 text-xl font-bold border-t-2"><span>Total:</span><span>{{ formatCurrency(sale.total_amount) }}</span></div>
        <div class="flex justify-between py-1"><span>Paid:</span><span>{{ formatCurrency(sale.paid_amount) }}</span></div>
        <div class="flex justify-between py-1"><span>Change:</span><span>{{ formatCurrency(sale.change_amount) }}</span></div>
      </div>
    </div>

    <!-- QR Code & Barcode -->
    <div class="flex justify-between items-center mb-8">
      <div v-if="settings.show_qr_code && qrCode" class="text-center">
        <img :src="`data:image/png;base64,${qrCode}`" class="w-32 h-32" />
        <p class="text-xs mt-2">Scan for PromptPay</p>
      </div>
      <div v-if="settings.show_barcode && barcode" class="text-center">
        <svg :id="`barcode-${sale.id}`" class="mx-auto"></svg>
        <p class="text-xs mt-2">{{ barcode }}</p>
      </div>
    </div>

    <!-- Notes -->
    <div v-if="settings.show_notes && settings.receipt_notes" class="text-center text-sm border-t pt-4">
      <p>{{ settings.receipt_notes }}</p>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import JsBarcode from 'jsbarcode';

const props = defineProps({ sale: Object, settings: Object, qrCode: String, barcode: String });

onMounted(() => {
  if (props.settings.show_barcode && props.barcode) {
    JsBarcode(`#barcode-${props.sale.id}`, props.barcode, { format: 'CODE128', width: 2, height: 50, displayValue: false });
  }
});

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleString('th-TH')
}

const formatCurrency = (amount) => {
  if (!amount || isNaN(amount)) return '฿0.00'
  return new Intl.NumberFormat('th-TH', { style: 'currency', currency: 'THB' }).format(amount)
}
</script>

<style>
@media print {
  @page { size: A4; margin: 0; }
  body { margin: 0; }
}
</style>
