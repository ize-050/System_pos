<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'refund_id',
        'product_id',
        'sale_item_id',
        'product_name',
        'quantity',
        'unit_price',
        'total_price',
        'return_to_stock',
        'condition',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'return_to_stock' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function refund()
    {
        return $this->belongsTo(Refund::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
    }

    /**
     * Accessors
     */
    public function getConditionLabelAttribute(): string
    {
        $labels = [
            'good' => 'สภาพดี',
            'damaged' => 'เสียหาย',
            'defective' => 'ชำรุด',
        ];
        return $labels[$this->condition] ?? ($this->condition ?? '-');
    }
}
