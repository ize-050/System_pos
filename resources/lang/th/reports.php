<?php

return [
    // Report Types
    'purchase_report' => 'รายงานยอดซื้อ',
    'sales_report' => 'รายงานยอดขาย',
    'inventory_report' => 'รายงานสต็อกสินค้า',
    'product_report' => 'รายงานสินค้า',
    'daily_report' => 'รายงานประจำวัน',
    
    // Actions
    'export_excel' => 'Export Excel',
    'export_pdf' => 'Export PDF',
    'generate_report' => 'สร้างรายงาน',
    'filter' => 'กรองข้อมูล',
    'reset_filter' => 'ล้างตัวกรอง',
    'view_detail' => 'ดูรายละเอียด',
    'download' => 'ดาวน์โหลด',
    
    // Filters
    'start_date' => 'วันที่เริ่มต้น',
    'end_date' => 'วันที่สิ้นสุด',
    'supplier' => 'ซัพพลายเออร์',
    'customer' => 'ลูกค้า',
    'category' => 'หมวดหมู่',
    'status' => 'สถานะ',
    'all' => 'ทั้งหมด',
    'per_page' => 'แสดงต่อหน้า',
    
    // Status
    'draft' => 'ฉบับร่าง',
    'pending' => 'รอดำเนินการ',
    'shipping' => 'กำลังจัดส่ง',
    'received' => 'รับสินค้าแล้ว',
    'cancelled' => 'ยกเลิก',
    'completed' => 'เสร็จสิ้น',
    'failed' => 'ล้มเหลว',
    
    // Summary
    'total_sales' => 'ยอดขายรวม',
    'total_purchases' => 'ยอดซื้อรวม',
    'total_orders' => 'จำนวนออเดอร์',
    'total_transactions' => 'จำนวนธุรกรรม',
    'average_order_value' => 'มูลค่าเฉลี่ยต่อออเดอร์',
    'profit_loss' => 'กำไร/ขาดทุน',
    'profit_margin' => 'อัตรากำไร',
    
    // Dashboard
    'todays_summary' => 'สรุปวันนี้',
    'sales_vs_purchases' => 'ยอดขาย vs ยอดซื้อ',
    'top_products' => 'สินค้าขายดี',
    'low_stock_alerts' => 'แจ้งเตือนสต็อกต่ำ',
    'revenue' => 'รายได้',
    'cost_of_goods' => 'ต้นทุนขาย',
    'gross_profit' => 'กำไรขั้นต้น',
    
    // Messages
    'no_data' => 'ไม่มีข้อมูล',
    'loading' => 'กำลังโหลด...',
    'exporting' => 'กำลัง Export...',
    'export_success' => 'Export สำเร็จ',
    'export_failed' => 'Export ล้มเหลว',
    'generating' => 'กำลังสร้างรายงาน...',
    'report_generated' => 'สร้างรายงานสำเร็จ',
    
    // Columns
    'po_number' => 'เลขที่ใบสั่งซื้อ',
    'sale_number' => 'เลขที่ใบเสร็จ',
    'order_date' => 'วันที่สั่งซื้อ',
    'sale_date' => 'วันที่ขาย',
    'report_date' => 'วันที่รายงาน',
    'product_name' => 'ชื่อสินค้า',
    'quantity' => 'จำนวน',
    'unit_price' => 'ราคาต่อหน่วย',
    'subtotal' => 'ยอดรวม',
    'discount' => 'ส่วนลด',
    'vat' => 'VAT 7%',
    'total' => 'ยอดรวมสุทธิ',
    
    // Stock
    'current_stock' => 'สต็อกปัจจุบัน',
    'minimum_stock' => 'สต็อกขั้นต่ำ',
    'reorder_quantity' => 'แนะนำสั่งซื้อ',
    'stock_value' => 'มูลค่าสต็อก',
    'low_stock' => 'สต็อกต่ำ',
    'out_of_stock' => 'สินค้าหมด',
    'normal_stock' => 'สต็อกปกติ',
    
    // Period
    '7_days' => '7 วัน',
    '30_days' => '30 วัน',
    '90_days' => '90 วัน',
    'this_month' => 'เดือนนี้',
    'last_month' => 'เดือนที่แล้ว',
    'this_year' => 'ปีนี้',
    
    // Errors
    'error_generating_report' => 'เกิดข้อผิดพลาดในการสร้างรายงาน',
    'error_exporting' => 'เกิดข้อผิดพลาดในการ Export',
    'error_loading_data' => 'เกิดข้อผิดพลาดในการโหลดข้อมูล',
    'record_limit_exceeded' => 'ข้อมูลเกิน 50,000 รายการ กรุณากรองข้อมูล',
];
