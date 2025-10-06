<?php

namespace App\Exports;

use App\Models\PurchaseOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PurchaseOrdersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return PurchaseOrder::with(['supplier', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'PO Number',
            'Supplier',
            'Order Date',
            'Expected Delivery',
            'Received Date',
            'Subtotal',
            'Discount',
            'VAT',
            'Total Amount',
            'Status',
            'Created By',
            'Created At',
        ];
    }

    /**
     * @param PurchaseOrder $po
     * @return array
     */
    public function map($po): array
    {
        return [
            $po->po_number,
            $po->supplier->name ?? '-',
            $po->order_date->format('Y-m-d'),
            $po->expected_delivery_date ? $po->expected_delivery_date->format('Y-m-d') : '-',
            $po->received_date ? $po->received_date->format('Y-m-d') : '-',
            $po->subtotal,
            $po->discount_amount,
            $po->vat_amount,
            $po->total_amount,
            ucfirst($po->status),
            $po->createdBy->name ?? '-',
            $po->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
