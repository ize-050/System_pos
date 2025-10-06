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

class SalesReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
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
        return collect($this->data)->map(function ($sale) {
            return [
                'sale_number' => $sale->sale_number ?? '',
                'sale_date' => $sale->sale_date ?? '',
                'customer_name' => $sale->customer->name ?? 'ลูกค้าทั่วไป',
                'cashier' => $sale->user->name ?? '',
                'subtotal' => $sale->subtotal ?? 0,
                'discount_amount' => $sale->discount_amount ?? 0,
                'vat_amount' => $sale->vat_amount ?? 0,
                'total_amount' => $sale->total_amount ?? 0,
                'payment_method' => $this->getPaymentMethodText($sale->payment_method ?? ''),
            ];
        });
    }

    /**
     * Define headings
     */
    public function headings(): array
    {
        return [
            'เลขที่ใบเสร็จ',
            'วันที่ขาย',
            'ลูกค้า',
            'พนักงานขาย',
            'ยอดรวม (บาท)',
            'ส่วนลด (บาท)',
            'VAT 7% (บาท)',
            'ยอดรวมสุทธิ (บาท)',
            'วิธีชำระเงิน',
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
                'startColor' => ['rgb' => 'D9E1F2'],
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
            'B' => 15,
            'C' => 25,
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
        return 'รายงานยอดขาย';
    }

    /**
     * Get payment method text in Thai
     */
    private function getPaymentMethodText($method)
    {
        $methodMap = [
            'cash' => 'เงินสด',
            'credit_card' => 'บัตรเครดิต',
            'debit_card' => 'บัตรเดบิต',
            'qr_code' => 'QR Code',
            'bank_transfer' => 'โอนเงิน',
        ];

        return $methodMap[$method] ?? $method;
    }
}
