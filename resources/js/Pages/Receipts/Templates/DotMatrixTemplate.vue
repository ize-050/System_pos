<template>
  <div class="dot-matrix-receipt" :style="receiptStyle">
    <!-- Header Section -->
    <div class="header-section">
      <!-- Left: Shop Info -->
      <div class="shop-info">
        <div class="shop-name">{{ settings.shop_name || 'ชื่อร้านค้า' }}</div>
        <div class="shop-detail">{{ settings.shop_address || '' }}</div>
        <div class="shop-detail" v-if="settings.shop_phone">โทร: {{ settings.shop_phone }}</div>
        <div class="shop-detail" v-if="settings.tax_id">เลขประจำตัวผู้เสียภาษี: {{ settings.tax_id }}</div>
        <div class="shop-detail">POS ID: {{ sale.sale_number }}</div>
      </div>
      
      <!-- Right: Document Info -->
      <div class="doc-info">
        <div class="doc-title-box">
          <div class="doc-note">ใบกำกับภาษี/ใบเสร็จรับเงิน</div>
          <div class="doc-note-en">TAX INVOICE</div>
        </div>
        <table class="doc-detail-table">
          <tr>
            <td class="doc-label">เลขที่ / NO.</td>
            <td class="doc-value">{{ sale.sale_number }}</td>
          </tr>
          <tr>
            <td class="doc-label">วันที่ / DATE</td>
            <td class="doc-value">{{ formatDateOnly(sale.created_at) }}</td>
          </tr>
          <tr>
            <td class="doc-label">CASHIER</td>
            <td class="doc-value">{{ sale.cashier?.name || '-' }}</td>
          </tr>
        </table>
      </div>
    </div>

    <!-- Customer Info Row -->
    <div class="customer-row">
      <table class="customer-table">
        <tr>
          <td class="cust-label">CUSTOMER NAME</td>
          <td class="cust-value" colspan="3">{{ sale.customer?.company_name || sale.customer?.name || 'ลูกค้าทั่วไป' }}</td>
        </tr>
        <tr>
          <td class="cust-label">ที่อยู่</td>
          <td class="cust-value" colspan="3">{{ sale.customer?.address || '-' }}</td>
        </tr>
        <tr v-if="sale.customer?.tax_id">
          <td class="cust-label">เลขประจำตัวผู้เสียภาษี</td>
          <td class="cust-value" colspan="3">{{ sale.customer.tax_id }}</td>
        </tr>
      </table>
    </div>

    <!-- Items Table -->
    <table class="items-table">
      <thead>
        <tr class="items-header">
          <th class="col-qty">QUANTITY<br>จำนวน</th>
          <th class="col-article">ARTICLE<br>NUMBER</th>
          <th class="col-desc">ARTICLE DESCRIPTION<br>รายการสินค้า</th>
          <th class="col-unit">UNIT<br>หน่วย</th>
          <th class="col-price">PACK<br>PRICE</th>
          <th class="col-vat">VAT</th>
          <th class="col-amount">VALUE INCLUDED<br>VAT (BAHT)</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in sale.sale_items" :key="item.id" class="item-row">
          <td class="text-center">{{ item.quantity }}</td>
          <td class="text-center">{{ item.product?.sku || '-' }}</td>
          <td>{{ item.product?.name || '-' }}</td>
          <td class="text-center">{{ item.product?.unit || 'ชิ้น' }}</td>
          <td class="text-right">{{ formatNumber(item.unit_price) }}</td>
          <td class="text-center">7%</td>
          <td class="text-right">{{ formatNumber(item.total_price) }}</td>
        </tr>
        <!-- Empty rows -->
        <tr v-for="n in Math.max(0, 8 - (sale.sale_items?.length || 0))" :key="'empty-' + n" class="item-row empty-row">
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </tbody>
    </table>

    <!-- Summary Section -->
    <div class="summary-section">
      <div class="summary-left">
        <div class="amount-words">({{ convertToThaiWords(sale.total_amount) }})</div>
      </div>
      <div class="summary-right">
        <table class="summary-table">
          <tr>
            <td class="sum-label">จำนวน รายการ / QTY</td>
            <td class="sum-label">ราคาสินค้า / LEGAL AMOUNT</td>
            <td class="sum-label">ภาษี / VAT</td>
            <td class="sum-label">รวม / TOTAL</td>
            <td class="sum-label">รวมเงิน / BAHT</td>
          </tr>
          <tr>
            <td class="sum-value">{{ getTotalQuantity() }}</td>
            <td class="sum-value">{{ formatNumber(getSubtotalBeforeVat()) }}</td>
            <td class="sum-value">{{ formatNumber(sale.vat_amount || 0) }}</td>
            <td class="sum-value">{{ formatNumber(sale.total_amount) }}</td>
            <td class="sum-value">{{ formatNumber(sale.total_amount) }}</td>
          </tr>
          <tr>
            <td class="sum-label">TOTAL</td>
            <td class="sum-value">{{ formatNumber(getSubtotalBeforeVat()) }}</td>
            <td class="sum-value">{{ formatNumber(sale.vat_amount || 0) }}</td>
            <td class="sum-value">{{ formatNumber(sale.total_amount) }}</td>
            <td class="sum-label">{{ formatPaymentMethod(sale.payment_method) }}</td>
          </tr>
          <tr v-if="sale.discount_amount > 0">
            <td class="sum-label">ส่วนลด</td>
            <td colspan="4" class="sum-value">{{ formatNumber(sale.discount_amount) }}</td>
          </tr>
          <tr>
            <td class="sum-label">รับเงิน</td>
            <td class="sum-value" colspan="2">{{ formatNumber(sale.paid_amount) }}</td>
            <td class="sum-label">เงินทอน</td>
            <td class="sum-value">{{ formatNumber(sale.change_amount) }}</td>
          </tr>
        </table>
      </div>
    </div>

    <!-- Footer -->
    <div class="footer-section">
      <div class="form-number">FORM BA003 VAT</div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  sale: {
    type: Object,
    required: true
  },
  settings: {
    type: Object,
    required: true
  }
})

// Receipt style for Dot Matrix (9.5" x 11" = 241mm x 279mm)
// ปรับให้พอดีกระดาษ A4 หรือ 9.5" x 11"
const receiptStyle = computed(() => ({
  width: '210mm',  // A4 width หรือปรับเป็น 241mm สำหรับ 9.5"
  minHeight: '270mm',
  padding: '5mm 8mm',
  fontFamily: '"Courier New", Courier, monospace',
  fontSize: '10pt',
  lineHeight: '1.3',
  backgroundColor: 'white',
  color: 'black'
}))

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleString('th-TH', { 
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatDateOnly = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('th-TH', { 
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  })
}

const formatNumber = (amount) => {
  if (!amount || isNaN(amount)) return '0.00'
  return new Intl.NumberFormat('th-TH', { 
    minimumFractionDigits: 2,
    maximumFractionDigits: 2 
  }).format(amount)
}

const formatPaymentMethod = (method) => {
  const methods = {
    'cash': 'CASH',
    'transfer': 'TRANSFER',
    'credit_card': 'CREDIT',
    'credit': 'CREDIT'
  }
  return methods[method] || method || '-'
}

const getTotalQuantity = () => {
  if (!props.sale.sale_items) return 0
  return props.sale.sale_items.reduce((sum, item) => sum + (item.quantity || 0), 0)
}

const getSubtotalBeforeVat = () => {
  const total = props.sale.total_amount || 0
  const vat = props.sale.vat_amount || 0
  return total - vat
}

// Convert number to Thai words
const convertToThaiWords = (amount) => {
  if (!amount || isNaN(amount)) return 'ศูนย์บาทถ้วน'
  
  const thaiNumbers = ['', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า']
  const thaiPositions = ['', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน']
  
  const num = Math.floor(amount)
  const satang = Math.round((amount - num) * 100)
  
  if (num === 0) {
    return satang > 0 ? `${satang} สตางค์` : 'ศูนย์บาทถ้วน'
  }
  
  let result = ''
  let numStr = num.toString()
  let len = numStr.length
  
  for (let i = 0; i < len; i++) {
    let digit = parseInt(numStr[i])
    let position = len - i - 1
    
    if (digit === 0) continue
    
    // Special cases
    if (position === 1 && digit === 1) {
      result += 'สิบ'
    } else if (position === 1 && digit === 2) {
      result += 'ยี่สิบ'
    } else if (position === 0 && digit === 1 && len > 1) {
      result += 'เอ็ด'
    } else {
      result += thaiNumbers[digit] + thaiPositions[position % 7]
    }
    
    if (position === 6 && len > 7) {
      result += 'ล้าน'
    }
  }
  
  result += 'บาท'
  
  if (satang > 0) {
    result += convertSatang(satang) + 'สตางค์'
  } else {
    result += 'ถ้วน'
  }
  
  return result
}

const convertSatang = (satang) => {
  const thaiNumbers = ['', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า']
  
  if (satang < 10) {
    return thaiNumbers[satang]
  }
  
  const tens = Math.floor(satang / 10)
  const ones = satang % 10
  
  let result = ''
  
  if (tens === 1) {
    result = 'สิบ'
  } else if (tens === 2) {
    result = 'ยี่สิบ'
  } else {
    result = thaiNumbers[tens] + 'สิบ'
  }
  
  if (ones === 1) {
    result += 'เอ็ด'
  } else if (ones > 0) {
    result += thaiNumbers[ones]
  }
  
  return result
}
</script>

<style scoped>
.dot-matrix-receipt {
  box-sizing: border-box;
  background: white;
  border: 1px solid #ccc;
}

/* Header Section */
.header-section {
  display: flex;
  justify-content: space-between;
  padding: 3mm;
  border-bottom: 1px solid #333;
}

.shop-info {
  flex: 1;
}

.shop-name {
  font-size: 11pt;
  font-weight: bold;
  margin-bottom: 1mm;
}

.shop-detail {
  font-size: 8pt;
  line-height: 1.4;
}

.doc-info {
  text-align: right;
  min-width: 55mm;
}

.doc-title-box {
  border: 1px solid #333;
  padding: 2mm;
  margin-bottom: 2mm;
  background: #f5f5f5;
}

.doc-note {
  font-size: 9pt;
  font-weight: bold;
}

.doc-note-en {
  font-size: 8pt;
}

.doc-detail-table {
  width: 100%;
  font-size: 8pt;
}

.doc-label {
  text-align: left;
  padding: 1mm 0;
}

.doc-value {
  text-align: right;
  padding: 1mm 0;
  border-bottom: 1px dotted #999;
}

/* Customer Row */
.customer-row {
  padding: 2mm 3mm;
  border-bottom: 1px solid #333;
}

.customer-table {
  width: 100%;
  font-size: 8pt;
}

.cust-label {
  width: 25%;
  padding: 1mm;
  font-weight: bold;
}

.cust-value {
  padding: 1mm;
  border-bottom: 1px dotted #999;
}

/* Items Table */
.items-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 8pt;
}

.items-header th {
  background: #f5f5f5;
  border: 1px solid #333;
  padding: 1.5mm;
  text-align: center;
  font-size: 7pt;
  line-height: 1.3;
}

.items-table td {
  border: 1px solid #333;
  padding: 1.5mm;
  font-size: 8pt;
}

.item-row td {
  height: 6mm;
}

.empty-row td {
  height: 6mm;
}

.col-qty { width: 8%; }
.col-article { width: 12%; }
.col-desc { width: 35%; }
.col-unit { width: 8%; }
.col-price { width: 12%; }
.col-vat { width: 8%; }
.col-amount { width: 17%; }

.text-center { text-align: center; }
.text-right { text-align: right; }

/* Summary Section */
.summary-section {
  display: flex;
  border-top: 1px solid #333;
}

.summary-left {
  flex: 1;
  padding: 3mm;
  border-right: 1px solid #333;
}

.amount-words {
  font-size: 9pt;
  font-weight: bold;
}

.summary-right {
  flex: 2;
}

.summary-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 7pt;
}

.summary-table td {
  border: 1px solid #333;
  padding: 1.5mm;
}

.sum-label {
  background: #f5f5f5;
  font-weight: bold;
  text-align: center;
}

.sum-value {
  text-align: right;
}

/* Footer Section */
.footer-section {
  padding: 2mm 3mm;
  border-top: 1px solid #333;
}

.form-number {
  font-size: 8pt;
  color: #666;
}

/* Print Styles */
@media print {
  .dot-matrix-receipt {
    width: 100%;
    min-height: auto;
    border: none;
    padding: 0;
  }
  
  .main-border {
    border-width: 1px;
  }
}
</style>
