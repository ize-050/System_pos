<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'po_number',
        'supplier_id',
        'order_date',
        'expected_delivery_date',
        'received_date',
        'subtotal',
        'discount_amount',
        'vat_amount',
        'total_amount',
        'status',
        'notes',
        'document_path',
        'created_by',
        'updated_by',
        'received_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'received_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'po_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }

    // Methods
    public function canTransitionTo($newStatus)
    {
        $allowed = [
            'draft' => ['pending', 'cancelled'],
            'pending' => ['shipping', 'cancelled'],
            'shipping' => ['received', 'cancelled'],
            'received' => [],
            'cancelled' => []
        ];

        return in_array($newStatus, $allowed[$this->status] ?? []);
    }

    public static function generatePONumber()
    {
        $year = date('Y');
        $lastPO = self::where('po_number', 'like', "PO-{$year}-%")
            ->orderBy('po_number', 'desc')
            ->first();

        if ($lastPO) {
            $lastNumber = (int) substr($lastPO->po_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "PO-{$year}-{$newNumber}";
    }
}
