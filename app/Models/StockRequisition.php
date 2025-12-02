<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockRequisition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'requisition_number',
        'requisition_date',
        'requester_name',
        'department',
        'project_name',
        'reason',
        'total_cost_amount',
        'total_items',
        'status',
        'created_by',
        'notes',
        'completed_at',
        'cancelled_at',
        'cancelled_reason',
    ];

    protected $casts = [
        'requisition_date' => 'date',
        'total_cost_amount' => 'decimal:2',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Generate unique requisition number
     */
    public static function generateRequisitionNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = "REQ-{$date}-";
        
        $lastRequisition = self::where('requisition_number', 'like', $prefix . '%')
            ->orderBy('requisition_number', 'desc')
            ->first();
        
        if ($lastRequisition) {
            $lastNumber = (int) substr($lastRequisition->requisition_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $newNumber;
    }

    /**
     * Relationships
     */
    public function items()
    {
        return $this->hasMany(StockRequisitionItem::class, 'requisition_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scopes
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('requisition_date', [$startDate, $endDate]);
    }

    /**
     * Complete the requisition and deduct stock
     */
    public function complete()
    {
        if ($this->status !== 'draft') {
            return false;
        }

        // Deduct stock for each item
        foreach ($this->items as $item) {
            $product = $item->product;
            if ($product) {
                $product->decrement('current_stock', $item->quantity);
                
                // Log stock movement
                InventoryMovement::create([
                    'product_id' => $product->id,
                    'type' => 'requisition',
                    'quantity' => -$item->quantity,
                    'reference_type' => 'stock_requisition',
                    'reference_id' => $this->id,
                    'notes' => "เบิกสินค้า: {$this->requisition_number}",
                    'created_by' => auth()->id(),
                ]);
            }
        }

        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return true;
    }

    /**
     * Cancel the requisition and restore stock
     */
    public function cancel($reason = null)
    {
        if ($this->status === 'cancelled') {
            return false;
        }

        // If was completed, restore stock
        if ($this->status === 'completed') {
            foreach ($this->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->increment('current_stock', $item->quantity);
                    
                    // Log stock movement
                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'requisition_cancel',
                        'quantity' => $item->quantity,
                        'reference_type' => 'stock_requisition',
                        'reference_id' => $this->id,
                        'notes' => "ยกเลิกใบเบิก: {$this->requisition_number}",
                        'created_by' => auth()->id(),
                    ]);
                }
            }
        }

        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_reason' => $reason,
        ]);

        return true;
    }

    /**
     * Calculate totals
     */
    public function calculateTotals()
    {
        $this->total_cost_amount = $this->items->sum('total_cost');
        $this->total_items = $this->items->sum('quantity');
        $this->save();
    }

    /**
     * Status label
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'draft' => 'ร่าง',
            'completed' => 'เสร็จสิ้น',
            'cancelled' => 'ยกเลิก',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Status color
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'draft' => 'yellow',
            'completed' => 'green',
            'cancelled' => 'red',
        ];
        return $colors[$this->status] ?? 'gray';
    }
}
