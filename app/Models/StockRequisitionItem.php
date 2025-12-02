<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRequisitionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'requisition_id',
        'product_id',
        'quantity',
        'cost_price',
        'total_cost',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'cost_price' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function requisition()
    {
        return $this->belongsTo(StockRequisition::class, 'requisition_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate total cost
     */
    public function calculateTotalCost()
    {
        $this->total_cost = $this->quantity * $this->cost_price;
        $this->save();
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Auto calculate total cost before saving
        static::saving(function ($item) {
            $item->total_cost = $item->quantity * $item->cost_price;
        });

        // Update requisition totals after save
        static::saved(function ($item) {
            if ($item->requisition) {
                $item->requisition->calculateTotals();
            }
        });

        // Update requisition totals after delete
        static::deleted(function ($item) {
            if ($item->requisition) {
                $item->requisition->calculateTotals();
            }
        });
    }
}
