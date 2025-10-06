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

class InventoryReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
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
                'current_stock' => $product->current_stock ?? 0,
                'minimum_stock' => $product->minimum_stock ?? 0,
                'cost_price' => $product->cost_price ?? 0,
                'selling_price' => $product->selling_price ?? 0,
                'stock_value' => ($product->current_stock ?? 0) * ($product->cost_price ?? 0),
                'status' => $this->getStockStatus($product),
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
            'สต็อกปัจจุบัน',
            'สต็อกขั้นต่ำ',
            'ราคาทุน (บาท)',
            'ราคาขาย (บาท)',
            'มูลค่าสต็อก (บาท)',
            'สถานะ',
        ];
    }

    /**
     * Apply styles
     */
    public function styles(Worksheet $sheet)
    {
        // Header style
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF2CC'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Apply borders to all data
        $rowCount = $this->data->count() + 1;
        $sheet->getStyle('A1:I' . $rowCount)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Number format for currency columns
        $sheet->getStyle('F2:H' . $rowCount)->getNumberFormat()
            ->setFormatCode('#,##0.00');

        // Number format for stock columns
        $sheet->getStyle('D2:E' . $rowCount)->getNumberFormat()
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
            'F' => 15,
            'G' => 15,
            'H' => 18,
            'I' => 15,
        ];
    }

    /**
     * Set sheet title
     */
    public function title(): string
    {
        return 'รายงานสต็อกสินค้า';
    }

    /**
     * Get stock status
     */
    private function getStockStatus($product)
    {
        $currentStock = $product->current_stock ?? 0;
        $minimumStock = $product->minimum_stock ?? 0;

        if ($currentStock <= 0) {
            return 'สินค้าหมด';
        } elseif ($currentStock <= $minimumStock) {
            return 'สต็อกต่ำ';
        } else {
            return 'ปกติ';
        }
    }
}
