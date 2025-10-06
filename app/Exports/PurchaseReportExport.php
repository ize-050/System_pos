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

class PurchaseReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
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
        return collect($this->data)->map(function ($po) {
            return [
                'po_number' => $po->po_number ?? '',
                'supplier' => $po->supplier->name ?? '',
                'order_date' => $po->order_date ?? '',
                'expected_delivery_date' => $po->expected_delivery_date ?? '',
                'subtotal' => $po->subtotal ?? 0,
                'discount_amount' => $po->discount_amount ?? 0,
                'vat_amount' => $po->vat_amount ?? 0,
                'total_amount' => $po->total_amount ?? 0,
                'status' => $this->getStatusText($po->status ?? ''),
            ];
        });
    }

    /**
     * Define headings
     */
    public function headings(): array
    {
        return [
            'เลขที่ใบสั่งซื้อ',
            'ซัพพลายเออร์',
            'วันที่สั่งซื้อ',
            'วันที่คาดว่าจะได้รับ',
            'ยอดรวม (บาท)',
            'ส่วนลด (บาท)',
            'VAT 7% (บาท)',
            'ยอดรวมสุทธิ (บาท)',
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
                'startColor' => ['rgb' => 'E2EFDA'],
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
        $sheet->getStyle('E2:H' . $rowCount)->getNumberFormat()
            ->setFormatCode('#,##0.00');

        return [];
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 25,
            'C' => 15,
            'D' => 20,
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
        return 'รายงานยอดซื้อ';
    }

    /**
     * Get status text in Thai
     */
    private function getStatusText($status)
    {
        $statusMap = [
            'draft' => 'ฉบับร่าง',
            'pending' => 'รอดำเนินการ',
            'shipping' => 'กำลังจัดส่ง',
            'received' => 'รับสินค้าแล้ว',
            'cancelled' => 'ยกเลิก',
        ];

        return $statusMap[$status] ?? $status;
    }
}
