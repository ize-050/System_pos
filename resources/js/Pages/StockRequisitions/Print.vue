<template>
  <div class="print-container">
    <!-- Print Content -->
    <div class="print-content" ref="printContent">
      <!-- Header -->
      <div class="text-center mb-6">
        <h1 class="text-2xl font-bold">ใบเบิกสินค้า</h1>
        <p class="text-lg">Stock Requisition</p>
      </div>

      <!-- Document Info -->
      <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
        <div>
          <p><strong>เลขที่:</strong> {{ requisition.requisition_number }}</p>
          <p><strong>วันที่:</strong> {{ formatDate(requisition.requisition_date) }}</p>
          <p><strong>ผู้เบิก:</strong> {{ requisition.requester_name }}</p>
        </div>
        <div class="text-right">
          <p><strong>แผนก:</strong> {{ requisition.department || '-' }}</p>
          <p><strong>โครงการ:</strong> {{ requisition.project_name || '-' }}</p>
          <p><strong>สถานะ:</strong> {{ getStatusLabel(requisition.status) }}</p>
        </div>
      </div>

      <!-- Reason -->
      <div v-if="requisition.reason" class="mb-4 p-3 bg-gray-50 rounded text-sm">
        <strong>เหตุผลการเบิก:</strong> {{ requisition.reason }}
      </div>

      <!-- Items Table -->
      <table class="w-full mb-6 text-sm">
        <thead>
          <tr class="border-y-2 border-black">
            <th class="py-2 text-left w-12">ลำดับ</th>
            <th class="py-2 text-left">รายการสินค้า</th>
            <th class="py-2 text-center w-24">จำนวน</th>
            <th class="py-2 text-right w-28">ราคาทุน/หน่วย</th>
            <th class="py-2 text-right w-28">จำนวนเงิน</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in requisition.items" :key="item.id" class="border-b border-dashed">
            <td class="py-2 text-center">{{ index + 1 }}</td>
            <td class="py-2">
              {{ item.product?.name }}
              <span class="text-gray-500 text-xs">({{ item.product?.sku }})</span>
            </td>
            <td class="py-2 text-center">{{ item.quantity }} {{ item.product?.unit || '' }}</td>
            <td class="py-2 text-right">{{ formatNumber(item.cost_price) }}</td>
            <td class="py-2 text-right">{{ formatNumber(item.total_cost) }}</td>
          </tr>
        </tbody>
        <tfoot>
          <tr class="border-t-2 border-black">
            <td colspan="4" class="py-3 text-right font-bold">รวมมูลค่าทั้งสิ้น:</td>
            <td class="py-3 text-right font-bold text-lg">{{ formatNumber(requisition.total_cost_amount) }} บาท</td>
          </tr>
        </tfoot>
      </table>

      <!-- Amount in Words -->
      <div class="mb-8 p-3 bg-gray-50 rounded text-center">
        <strong>({{ convertToThaiWords(requisition.total_cost_amount) }})</strong>
      </div>

      <!-- Notes -->
      <div v-if="requisition.notes" class="mb-8 text-sm">
        <strong>หมายเหตุ:</strong> {{ requisition.notes }}
      </div>

      <!-- Signatures -->
      <div class="grid grid-cols-2 gap-8 mt-12">
        <div class="text-center">
          <div class="border-b border-black mb-2 h-16"></div>
          <p class="font-medium">ผู้เบิก</p>
          <p class="text-sm text-gray-600">{{ requisition.requester_name }}</p>
          <p class="text-sm text-gray-500">วันที่ ____/____/____</p>
        </div>
        <div class="text-center">
          <div class="border-b border-black mb-2 h-16"></div>
          <p class="font-medium">ผู้อนุมัติ</p>
          <p class="text-sm text-gray-600">________________________</p>
          <p class="text-sm text-gray-500">วันที่ ____/____/____</p>
        </div>
      </div>

      <!-- Footer -->
      <div class="mt-8 pt-4 border-t text-center text-xs text-gray-500">
        <p>พิมพ์เมื่อ: {{ formatDateTime(new Date()) }}</p>
        <p>ผู้พิมพ์: {{ requisition.created_by?.name || '-' }}</p>
      </div>
    </div>

    <!-- Print Actions (hidden when printing) -->
    <div class="print-actions no-print">
      <button @click="printDocument" class="print-btn">🖨️ พิมพ์</button>
      <button @click="goBack" class="close-btn">← กลับ</button>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  requisition: Object,
})

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('th-TH', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const formatDateTime = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleString('th-TH')
}

const formatNumber = (num) => {
  if (!num) return '0.00'
  return new Intl.NumberFormat('th-TH', { minimumFractionDigits: 2 }).format(num)
}

const getStatusLabel = (status) => {
  const labels = {
    draft: 'ร่าง',
    completed: 'เสร็จสิ้น',
    cancelled: 'ยกเลิก',
  }
  return labels[status] || status
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
    if (satang < 10) {
      result += thaiNumbers[satang] + 'สตางค์'
    } else {
      const tens = Math.floor(satang / 10)
      const ones = satang % 10
      if (tens === 1) {
        result += 'สิบ'
      } else if (tens === 2) {
        result += 'ยี่สิบ'
      } else {
        result += thaiNumbers[tens] + 'สิบ'
      }
      if (ones === 1) {
        result += 'เอ็ด'
      } else if (ones > 0) {
        result += thaiNumbers[ones]
      }
      result += 'สตางค์'
    }
  } else {
    result += 'ถ้วน'
  }
  
  return result
}

const printDocument = () => {
  window.print()
}

const goBack = () => {
  router.visit(route('stock-requisitions.show', props.requisition.id))
}

onMounted(() => {
  // Auto print after 500ms
  setTimeout(() => {
    window.print()
  }, 500)
})
</script>

<style scoped>
.print-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
  font-family: 'Sarabun', 'TH Sarabun New', sans-serif;
}

.print-content {
  background: white;
  padding: 40px;
  border: 1px solid #ddd;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.print-actions {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-top: 20px;
}

.print-btn, .close-btn {
  padding: 10px 24px;
  border: 1px solid #333;
  background: #fff;
  cursor: pointer;
  font-size: 14px;
  border-radius: 6px;
  transition: all 0.3s;
}

.print-btn {
  background: #4CAF50;
  color: white;
  border-color: #4CAF50;
}

.print-btn:hover {
  background: #45a049;
}

.close-btn:hover {
  background: #f0f0f0;
}

@media print {
  .no-print {
    display: none !important;
  }
  
  .print-container {
    padding: 0;
    max-width: 100%;
  }
  
  .print-content {
    border: none;
    box-shadow: none;
    padding: 20px;
  }
}
</style>
