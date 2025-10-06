<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_id',
        'product_id',
        'product_name',
        'quantity_ordered',
        'quantity_received',
        'unit_price',
        'total_price',
        'condition_status',
        'notes',
    ];

    protected $casts = [
        'quantity_ordered' => 'decimal:2',
        'quantity_received' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    // Relationships
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accessors
    public function getVarianceAttribute()
    {
        return $this->quantity_received - $this->quantity_ordered;
    }
}
