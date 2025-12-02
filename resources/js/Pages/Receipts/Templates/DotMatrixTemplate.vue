<template>
  <div class="dot-matrix-receipt" :style="receiptStyle">
    <!-- Header -->
    <div class="text-center mb-4">
      <div class="text-xl font-bold tracking-wide">{{ settings.shop_name || 'ร้านค้า' }}</div>
      <div>{{ settings.shop_address || '' }}</div>
      <div>โทร: {{ settings.shop_phone || '-' }}</div>
      <div v-if="settings.tax_id">เลขประจำตัวผู้เสียภาษี: {{ settings.tax_id }}</div>
    </div>

    <!-- Document Title -->
    <div class="text-center text-lg font-bold border-y border-black py-2 mb-4">
      ใบเสร็จรับเงิน / ใบกำกับภาษีอย่างย่อ
    </div>

    <!-- Receipt Info -->
    <div class="grid grid-cols-2 gap-2 mb-4 text-sm">
      <div>เลขที่: {{ sale.sale_number }}</div>
      <div class="text-right">วันที่: {{ formatDate(sale.created_at) }}</div>
      <div v-if="sale.customer" class="col-span-2">ลูกค้า: {{ sale.customer.name || sale.customer.company_name }}</div>
      <div v-if="sale.customer?.phone" class="col-span-2">โทร: {{ sale.customer.phone }}</div>
      <div v-if="sale.customer?.address" class="col-span-2">ที่อยู่: {{ sale.customer.address }}</div>
      <div class="col-span-2">พนักงาน: {{ sale.cashier?.name || '-' }}</div>
    </div>

    <!-- Items Table -->
    <table class="w-full mb-4">
      <thead>
        <tr class="border-y border-black">
          <th class="text-left py-1 w-8">ลำดับ</th>
          <th class="text-left py-1">รายการสินค้า</th>
          <th class="text-right py-1 w-16">จำนวน</th>
          <th class="text-right py-1 w-24">ราคา/หน่วย</th>
          <th class="text-right py-1 w-24">จำนวนเงิน</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in sale.sale_items" :key="item.id" class="border-b border-dashed">
          <td class="py-1 text-center">{{ index + 1 }}</td>
          <td class="py-1">{{ item.product?.name || '-' }}</td>
          <td class="text-right py-1">{{ item.quantity }}</td>
          <td class="text-right py-1">{{ formatNumber(item.unit_price) }}</td>
          <td class="text-right py-1">{{ formatNumber(item.total_price) }}</td>
        </tr>
      </tbody>
    </table>

    <!-- Summary -->
    <div class="border-t border-black pt-2">
      <div class="flex justify-between py-1">
        <span>รวมเป็นเงิน:</span>
        <span>{{ formatNumber(sale.subtotal) }} บาท</span>
      </div>
      <div v-if="sale.discount_amount > 0" class="flex justify-between py-1">
        <span>ส่วนลด:</span>
        <span>{{ formatNumber(sale.discount_amount) }} บาท</span>
      </div>
      <div v-if="settings.show_vat && sale.vat_amount" class="flex justify-between py-1">
        <span>ภาษีมูลค่าเพิ่ม 7%:</span>
        <span>{{ formatNumber(sale.vat_amount) }} บาท</span>
      </div>
      <div class="flex justify-between py-2 text-lg font-bold border-t border-black">
        <span>ยอดสุทธิ:</span>
        <span>{{ formatNumber(sale.total_amount) }} บาท</span>
      </div>
      <div class="flex justify-between py-1">
        <span>ชำระโดย: {{ formatPaymentMethod(sale.payment_method) }}</span>
        <span></span>
      </div>
      <div class="flex justify-between py-1">
        <span>รับเงิน:</span>
        <span>{{ formatNumber(sale.paid_amount) }} บาท</span>
      </div>
      <div class="flex justify-between py-1">
        <span>เงินทอน:</span>
        <span>{{ formatNumber(sale.change_amount) }} บาท</span>
      </div>
    </div>

    <!-- Amount in Words -->
    <div class="border-t border-black mt-4 pt-2">
      <div class="text-center">
        ({{ convertToThaiWords(sale.total_amount) }})
      </div>
    </div>

    <!-- Footer Notes -->
    <div v-if="settings.show_notes && settings.receipt_notes" class="text-center mt-4 pt-2 border-t border-dashed">
      {{ settings.receipt_notes }}
    </div>

    <!-- Signature -->
    <div class="grid grid-cols-2 gap-8 mt-8 pt-4">
      <div class="text-center">
        <div class="border-b border-black mb-2 h-12"></div>
        <div>ผู้รับเงิน</div>
      </div>
      <div class="text-center">
        <div class="border-b border-black mb-2 h-12"></div>
        <div>ผู้จ่ายเงิน</div>
      </div>
    </div>

    <!-- Thank You -->
    <div class="text-center mt-6 text-sm">
      *** ขอบคุณที่ใช้บริการ ***
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
const receiptStyle = computed(() => ({
  width: '241mm',
  minHeight: '279mm',
  padding: '10mm 15mm',
  fontFamily: '"Courier New", Courier, monospace',
  fontSize: '12pt',
  lineHeight: '1.4',
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

const formatNumber = (amount) => {
  if (!amount || isNaN(amount)) return '0.00'
  return new Intl.NumberFormat('th-TH', { 
    minimumFractionDigits: 2,
    maximumFractionDigits: 2 
  }).format(amount)
}

const formatPaymentMethod = (method) => {
  const methods = {
    'cash': 'เงินสด',
    'transfer': 'โอนเงิน',
    'credit_card': 'บัตรเครดิต',
    'credit': 'เครดิต/บัญชีลูกค้า'
  }
  return methods[method] || method || '-'
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
  border: 1px solid #ccc;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.dot-matrix-receipt table {
  border-collapse: collapse;
}

.dot-matrix-receipt th,
.dot-matrix-receipt td {
  font-family: "Courier New", Courier, monospace;
}

@media print {
  .dot-matrix-receipt {
    border: none;
    box-shadow: none;
    width: 100%;
    min-height: auto;
  }
}
</style>
