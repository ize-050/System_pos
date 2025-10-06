<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class CategoryTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        // ส่งคืนแถวตัวอย่าง 3 แถว
        return [
            [
                'อุปกรณ์ก่อสร้าง',
                'เครื่องมือและอุปกรณ์สำหรับงานก่อสร้าง',
                null, // parent_id (null สำหรับหมวดหมู่หลัก)
                1 // is_active (1 = ใช้งาน, 0 = ไม่ใช้งาน)
            ],
            [
                'เครื่องมือไฟฟ้า',
                'สว่าน เลื่อย เครื่องขัด',
                1, // parent_id (1 = หมวดหมู่ย่อยของอุปกรณ์ก่อสร้าง)
                1
            ],
            [
                'วัสดุก่อสร้าง',
                'ปูน อิฐ เหล็ก และวัสดุก่อสร้างอื่นๆ',
                null,
                1
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'ชื่อหมวดหมู่',
            'คำอธิบาย',
            'หมวดหมู่แม่_ID',
            'สถานะการใช้งาน'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as header
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25, // ชื่อหมวดหมู่
            'B' => 40, // คำอธิบาย
            'C' => 15, // หมวดหมู่แม่_ID
            'D' => 20, // สถานะการใช้งาน
        ];
    }
}