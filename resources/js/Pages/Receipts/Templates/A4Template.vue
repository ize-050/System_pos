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

    <!-- Tax Invoice Badge -->
    <div v-if="sale.invoice_type === 'tax_invoice'" class="text-center mb-4">
      <span class="inline-block bg-green-100 text-green-800 text-lg font-bold px-6 py-2 rounded-lg border-2 border-green-500">
        ใบกำกับภาษี / TAX INVOICE
      </span>
    </div>

    <!-- Receipt Info -->
    <div class="border-t border-b py-4 mb-4">
      <div class="grid grid-cols-2 gap-4 text-sm">
        <div><strong>เลขที่ / Receipt No:</strong> {{ sale.sale_number }}</div>
        <div class="text-right"><strong>วันที่ / Date:</strong> {{ formatDate(sale.created_at) }}</div>
        
        <!-- Tax Invoice Customer Info -->
        <template v-if="sale.invoice_type === 'tax_invoice' && sale.tax_invoice_customer">
          <div class="col-span-2 mt-2 p-3 bg-gray-50 rounded border">
            <p class="font-bold text-base mb-1">ข้อมูลผู้ซื้อ / Customer Information:</p>
            <p><strong>ชื่อ / Name:</strong> {{ sale.tax_invoice_customer.company_name || '-' }}</p>
            <p><strong>ที่อยู่ / Address:</strong> {{ sale.tax_invoice_customer.address || '-' }}</p>
            <p v-if="sale.tax_invoice_customer.tax_id"><strong>เลขประจำตัวผู้เสียภาษี / Tax ID:</strong> {{ sale.tax_invoice_customer.tax_id }}</p>
            <p v-if="sale.tax_invoice_customer.phone"><strong>โทร / Tel:</strong> {{ sale.tax_invoice_customer.phone }}</p>
            <p v-if="sale.tax_invoice_customer.branch"><strong>สาขา / Branch:</strong> {{ sale.tax_invoice_customer.branch }}</p>
          </div>
        </template>
        
        <!-- Regular Customer Info -->
        <template v-else>
          <div v-if="settings.show_customer_info && sale.customer"><strong>ลูกค้า / Customer:</strong> {{ sale.customer.name || sale.customer.company_name }}</div>
        </template>
        
        <div class="text-right"><strong>พนักงาน / Cashier:</strong> {{ sale.cashier?.name }}</div>
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
        <div v-if="sale.discount_amount > 0" class="flex justify-between py-1 text-green-600"><span>ส่วนลด:</span><span>-{{ formatCurrency(sale.discount_amount) }}</span></div>
        <div v-if="sale.shipping_fee > 0" class="flex justify-between py-1"><span>ค่าจัดส่ง:</span><span>{{ formatCurrency(sale.shipping_fee) }}</span></div>
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

    <!-- Notes - Hidden for cleaner receipt -->
    <!-- <div v-if="settings.show_notes && settings.receipt_notes" class="text-center text-sm border-t pt-4">
      <p>{{ settings.receipt_notes }}</p>
    </div> -->
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
