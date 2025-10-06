<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProductReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Return collection of data
     */
    public function collection()
    {
        return collect($this->data)->map(function ($product) {
            return [
                'sku' => $product->sku ?? '',
                'name' => $product->name ?? '',
                'category' => $product->category->name ?? '',
                'cost_price' => $product->cost_price ?? 0,
                'selling_price' => $product->selling_price ?? 0,
                'profit_margin' => $this->calculateProfitMargin($product),
                'current_stock' => $product->current_stock ?? 0,
                'is_active' => $product->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน',
            ];
        });
    }

    /**
     * Define headings
     */
    public function headings(): array
    {
        return [
            'รหัสสินค้า',
            'ชื่อสินค้า',
            'หมวดหมู่',
            'ราคาทุน (บาท)',
            'ราคาขาย (บาท)',
            'กำไร (%)',
            'สต็อก',
            'สถานะ',
        ];
    }

    /**
     * Apply styles
     */
    public function styles(Worksheet $sheet)
    {
        // Header style
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FCE4D6'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Apply borders to all data
        $rowCount = $this->data->count() + 1;
        $sheet->getStyle('A1:H' . $rowCount)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Number format for currency columns
        $sheet->getStyle('D2:E' . $rowCount)->getNumberFormat()
            ->setFormatCode('#,##0.00');

        // Number format for percentage
        $sheet->getStyle('F2:F' . $rowCount)->getNumberFormat()
            ->setFormatCode('0.00"%"');

        // Number format for stock
        $sheet->getStyle('G2:G' . $rowCount)->getNumberFormat()
            ->setFormatCode('#,##0');

        return [];
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 30,
            'C' => 20,
            'D' => 15,
            'E' => 15,
            'F' => 12,
            'G' => 12,
            'H' => 12,
        ];
    }

    /**
     * Set sheet title
     */
    public function title(): string
    {
        return 'รายงานสินค้า';
    }

    /**
     * Calculate profit margin percentage
     */
    private function calculateProfitMargin($product)
    {
        $costPrice = $product->cost_price ?? 0;
        $sellingPrice = $product->selling_price ?? 0;

        if ($sellingPrice == 0) {
            return 0;
        }

        return (($sellingPrice - $costPrice) / $sellingPrice) * 100;
    }
}
