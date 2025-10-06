<template>
    <div class="receipt-container">
        <div class="receipt">
            <!-- Header -->
            <div class="header">
                <div class="shop-name">ร้านค้าปลีก POS</div>
                <div class="shop-info">
                    <div>123 ถนนสุขุมวิท แขวงคลองตัน</div>
                    <div>เขตวัฒนา กรุงเทพฯ 10110</div>
                    <div>โทร: 02-123-4567</div>
                </div>
            </div>

            <!-- Receipt Info -->
            <div class="receipt-info">
                <div class="info-row">
                    <span>เลขที่ใบเสร็จ:</span>
                    <span>#{{ sale.id.toString().padStart(6, '0') }}</span>
                </div>
                <div class="info-row">
                    <span>วันที่:</span>
                    <span>{{ formatDateTime(sale.created_at) }}</span>
                </div>
                <div class="info-row">
                    <span>พนักงานขาย:</span>
                    <span>{{ sale.cashier?.name || 'ไม่ระบุ' }}</span>
                </div>
                <div v-if="sale.customer_name" class="info-row">
                    <span>ลูกค้า:</span>
                    <span>{{ sale.customer_name }}</span>
                </div>
                <div v-if="sale.customer_phone" class="info-row">
                    <span>เบอร์โทร:</span>
                    <span>{{ sale.customer_phone }}</span>
                </div>
            </div>

            <!-- Items -->
            <div class="items">
                <div class="items-header">
                    <div class="item-name">รายการสินค้า</div>
                    <div class="item-qty">จำนวน</div>
                    <div class="item-price">ราคา</div>
                    <div class="item-total">รวม</div>
                </div>
                <div class="divider"></div>
                
                <div v-for="item in sale.sale_items" :key="item.id" class="item">
                    <div class="item-name">{{ item.product?.name || 'สินค้าไม่พบ' }}</div>
                    <div class="item-qty">{{ item.quantity }}</div>
                    <div class="item-price">฿{{ formatPrice(item.unit_price) }}</div>
                    <div class="item-total">฿{{ formatPrice(item.total_price) }}</div>
                </div>
            </div>

            <!-- Totals -->
            <div class="totals">
                <div class="divider"></div>
                <div class="total-line">
                    <span>ยอดรวม:</span>
                    <span>฿{{ formatPrice(sale.subtotal) }}</span>
                </div>
                <div v-if="sale.discount_amount > 0" class="total-line">
                    <span>ส่วนลด:</span>
                    <span>-฿{{ formatPrice(sale.discount_amount) }}</span>
                </div>
                <div v-if="sale.tax_amount > 0" class="total-line">
                    <span>ภาษี:</span>
                    <span>฿{{ formatPrice(sale.tax_amount) }}</span>
                </div>
                <div class="total-line grand-total">
                    <span>ยอดรวมสุทธิ:</span>
                    <span>฿{{ formatPrice(sale.total_amount) }}</span>
                </div>
                <div class="total-line">
                    <span>รับเงิน:</span>
                    <span>฿{{ formatPrice(sale.payment_received) }}</span>
                </div>
                <div v-if="sale.change_amount > 0" class="total-line">
                    <span>เงินทอน:</span>
                    <span>฿{{ formatPrice(sale.change_amount) }}</span>
                </div>
                <div class="total-line">
                    <span>วิธีชำระเงิน:</span>
                    <span>{{ formatPaymentMethod(sale.payment_method) }}</span>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <div class="divider"></div>
                <div class="thank-you">ขอบคุณที่ใช้บริการ</div>
                <div class="note">โปรดเก็บใบเสร็จไว้เป็นหลักฐาน</div>
                <div v-if="sale.notes" class="notes">
                    <div>หมายเหตุ: {{ sale.notes }}</div>
                </div>
            </div>

            <!-- Print Button -->
            <div class="print-actions no-print">
                <button @click="printReceipt" class="print-btn">พิมพ์ใบเสร็จ</button>
                <button @click="closeWindow" class="close-btn">ปิด</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue'

const props = defineProps({
    sale: Object
})

const formatPrice = (price) => {
    return parseFloat(price || 0).toFixed(2)
}

const formatDateTime = (dateTime) => {
    return new Date(dateTime).toLocaleString('th-TH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const formatPaymentMethod = (method) => {
    const methods = {
        'cash': 'เงินสด',
        'card': 'บัตรเครดิต/เดบิต',
        'bank_transfer': 'โอนเงิน',
        'e_wallet': 'กระเป๋าเงินอิเล็กทรอนิกส์'
    }
    return methods[method] || method
}

const printReceipt = () => {
    window.print()
}

const closeWindow = () => {
    window.close()
}

onMounted(() => {
    // Auto print after page loads
    setTimeout(() => {
        window.print()
    }, 1000)
})
</script>

<style scoped>
.receipt-container {
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
    padding: 20px;
    font-family: 'Courier New', monospace;
    background: white;
}

.receipt {
    width: 100%;
    font-size: 12px;
    line-height: 1.4;
    color: #000;
}

.header {
    text-align: center;
    margin-bottom: 15px;
}

.shop-name {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 8px;
}

.shop-info {
    font-size: 11px;
    line-height: 1.3;
}

.receipt-info {
    margin-bottom: 15px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 3px;
}

.items {
    margin-bottom: 15px;
}

.items-header {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 8px;
    font-weight: bold;
    margin-bottom: 5px;
    text-align: center;
}

.items-header .item-name {
    text-align: left;
}

.item {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 8px;
    margin-bottom: 3px;
    text-align: center;
}

.item .item-name {
    text-align: left;
    word-wrap: break-word;
}

.divider {
    border-top: 1px dashed #000;
    margin: 8px 0;
}

.totals {
    margin-bottom: 15px;
}

.total-line {
    display: flex;
    justify-content: space-between;
    margin-bottom: 3px;
}

.grand-total {
    font-weight: bold;
    font-size: 14px;
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
    padding: 5px 0;
    margin: 8px 0;
}

.footer {
    text-align: center;
    margin-top: 15px;
}

.thank-you {
    font-weight: bold;
    margin-bottom: 5px;
}

.note {
    font-size: 10px;
    margin-bottom: 5px;
}

.notes {
    font-size: 10px;
    margin-top: 10px;
    text-align: left;
}

.print-actions {
    text-align: center;
    margin-top: 20px;
    gap: 10px;
    display: flex;
    justify-content: center;
}

.print-btn, .close-btn {
    padding: 8px 16px;
    border: 1px solid #333;
    background: #fff;
    cursor: pointer;
    font-size: 12px;
}

.print-btn:hover, .close-btn:hover {
    background: #f0f0f0;
}

/* Print styles */
@media print {
    .receipt-container {
        width: 80mm;
        max-width: none;
        margin: 0;
        padding: 0;
    }
    
    .receipt {
        font-size: 10px;
    }
    
    .shop-name {
        font-size: 14px;
    }
    
    .grand-total {
        font-size: 12px;
    }
    
    .no-print {
        display: none !important;
    }
    
    body {
        margin: 0;
        padding: 0;
    }
}

@page {
    size: 80mm auto;
    margin: 0;
}
</style>