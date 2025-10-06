<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ProductTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        // Return empty array for template (just headers)
        return [];
    }

    public function headings(): array
    {
        return [
            'รหัสสินค้า (SKU)', // sku
            'บาร์โค้ด', // barcode
            'ชื่อสินค้า*', // name (required)
            'รายละเอียด', // description
            'หมวดหมู่ (ID)*', // category_id (required)
            'หน่วย', // unit
            'ยี่ห้อ', // brand
            'รุ่น', // model
            'ขนาด', // size
            'สี', // color
            'วัสดุ', // material
            'น้ำหนัก (กก.)', // weight
            'ขนาด (กว้าง x ยาว x สูง)', // dimensions
            'ระยะเวลาการรับประกัน', // warranty_period
            'ผู้จำหน่าย', // supplier
            'ประเทศต้นกำเนิด', // origin_country
            'ข้อมูลจำเพาะ', // specifications
            'คำแนะนำการใช้งาน', // usage_instructions
            'คำเตือนด้านความปลอดภัย', // safety_warnings
            'เงื่อนไขการเก็บรักษา', // storage_conditions
            'วันที่ผลิต (YYYY-MM-DD)', // manufacturing_date
            'วันหมดอายุ (YYYY-MM-DD)', // expiry_date
            'การรับรอง', // certification
            'ราคาทุน*', // cost_price (required)
            'ราคาขาย*', // selling_price (required)
            'สต็อกปัจจุบัน', // current_stock
            'จุดสั่งซื้อใหม่', // reorder_point
            'เปิดใช้งาน (1=เปิด, 0=ปิด)', // is_active
            'หมายเหตุ', // notes
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as header
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFE2E8F0',
                    ],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // SKU
            'B' => 15, // Barcode
            'C' => 25, // Name
            'D' => 30, // Description
            'E' => 12, // Category ID
            'F' => 10, // Unit
            'G' => 15, // Brand
            'H' => 15, // Model
            'I' => 10, // Size
            'J' => 10, // Color
            'K' => 12, // Material
            'L' => 12, // Weight
            'M' => 20, // Dimensions
            'N' => 15, // Warranty
            'O' => 15, // Supplier
            'P' => 15, // Origin Country
            'Q' => 20, // Specifications
            'R' => 20, // Usage Instructions
            'S' => 20, // Safety Warnings
            'T' => 20, // Storage Conditions
            'U' => 15, // Manufacturing Date
            'V' => 15, // Expiry Date
            'W' => 15, // Certification
            'X' => 12, // Cost Price
            'Y' => 12, // Selling Price
            'Z' => 12, // Current Stock
            'AA' => 12, // Reorder Point
            'AB' => 15, // Is Active
            'AC' => 20, // Notes
        ];
    }
}