<template>
  <div class="thermal-receipt" :style="receiptStyle" style="border: 1px solid #ddd; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background: white;">
    <div class="text-center mb-2">
      <div class="font-bold text-lg">{{ settings.shop_name }}</div>
      <div class="text-xs">{{ settings.shop_address }}</div>
      <div class="text-xs">Tel: {{ settings.shop_phone }}</div>
      <div v-if="settings.tax_id" class="text-xs">Tax ID: {{ settings.tax_id }}</div>
    </div>

    <!-- Tax Invoice Badge -->
    <div v-if="sale.invoice_type === 'tax_invoice'" class="text-center my-2 py-1 border border-dashed">
      <div class="font-bold text-sm">ใบกำกับภาษี</div>
      <div class="text-xs">TAX INVOICE</div>
    </div>

    <div class="border-t border-b border-dashed py-2 my-2 text-xs">
      <div>เลขที่: {{ sale.sale_number }}</div>
      <div>วันที่: {{ formatDate(sale.created_at) }}</div>
      
      <!-- Tax Invoice Customer Info -->
      <template v-if="sale.invoice_type === 'tax_invoice' && sale.tax_invoice_customer">
        <div class="mt-1 pt-1 border-t border-dashed">
          <div class="font-bold">ข้อมูลผู้ซื้อ:</div>
          <div>{{ sale.tax_invoice_customer.company_name }}</div>
          <div v-if="sale.tax_invoice_customer.address" class="text-xs">{{ sale.tax_invoice_customer.address }}</div>
          <div v-if="sale.tax_invoice_customer.tax_id">Tax ID: {{ sale.tax_invoice_customer.tax_id }}</div>
        </div>
      </template>
      
      <!-- Regular Customer -->
      <template v-else>
        <div v-if="sale.customer">ลูกค้า: {{ sale.customer.name || sale.customer.company_name }}</div>
      </template>
    </div>

    <table class="w-full text-xs mb-2">
      <tbody>
        <tr v-for="item in sale.sale_items" :key="item.id" class="border-b border-dashed">
          <td class="py-1">{{ item.product?.name }}</td>
          <td class="text-right py-1">{{ item.quantity }}</td>
          <td class="text-right py-1">{{ formatCurrency(item.total_price) }}</td>
        </tr>
      </tbody>
    </table>

    <div class="border-t border-dashed pt-2 text-xs">
      <div class="flex justify-between"><span>Subtotal:</span><span>{{ formatCurrency(sale.subtotal) }}</span></div>
      <div v-if="sale.discount_amount > 0" class="flex justify-between text-green-600"><span>ส่วนลด:</span><span>-{{ formatCurrency(sale.discount_amount) }}</span></div>
      <div v-if="sale.shipping_fee > 0" class="flex justify-between"><span>ค่าจัดส่ง:</span><span>{{ formatCurrency(sale.shipping_fee) }}</span></div>
      <div v-if="settings.show_vat" class="flex justify-between"><span>VAT 7%:</span><span>{{ formatCurrency(sale.vat_amount) }}</span></div>
      <div class="flex justify-between font-bold text-base"><span>TOTAL:</span><span>{{ formatCurrency(sale.total_amount) }}</span></div>
      <div class="flex justify-between"><span>Paid:</span><span>{{ formatCurrency(sale.paid_amount) }}</span></div>
      <div class="flex justify-between"><span>Change:</span><span>{{ formatCurrency(sale.change_amount) }}</span></div>
    </div>

    <div v-if="settings.show_qr_code && qrCode" class="text-center my-4">
      <img :src="`data:image/png;base64,${qrCode}`" class="w-24 h-24 mx-auto" />
      <div class="text-xs">Scan for PromptPay</div>
    </div>

    <div v-if="settings.show_barcode && barcode" class="text-center my-2">
      <svg :id="`barcode-thermal-${sale.id}`" class="mx-auto"></svg>
    </div>

    <div v-if="settings.show_notes && settings.receipt_notes" class="text-center text-xs border-t border-dashed pt-2 mt-2">
      {{ settings.receipt_notes }}
    </div>

    <div class="text-center text-xs mt-4">*** Thank You ***</div>
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue';
import JsBarcode from 'jsbarcode';

const props = defineProps({ sale: Object, settings: Object, qrCode: String, barcode: String });

// Dynamic width based on printer type
const receiptWidth = computed(() => {
  return props.settings.printer_type === 'thermal_58mm' ? '58mm' : '80mm';
});

const receiptStyle = computed(() => ({
  width: receiptWidth.value,
  padding: '5mm',
  fontFamily: 'monospace',
  fontSize: props.settings.printer_type === 'thermal_58mm' ? '10px' : '12px'
}));

onMounted(() => {
  if (props.settings.show_barcode && props.barcode) {
    JsBarcode(`#barcode-thermal-${props.sale.id}`, props.barcode, { format: 'CODE128', width: 1, height: 30, displayValue: false });
  }
});

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleString('th-TH', { dateStyle: 'short', timeStyle: 'short' })
}

const formatCurrency = (amount) => {
  if (!amount || isNaN(amount)) return '฿0.00'
  return new Intl.NumberFormat('th-TH', { style: 'currency', currency: 'THB' }).format(amount)
}
</script>

<style>
@media print {
  @page { size: 80mm auto; margin: 0; }
  body { margin: 0; }
  .thermal-receipt[style*="58mm"] { width: 58mm !important; }
  .thermal-receipt[style*="80mm"] { width: 80mm !important; }
}
</style>
