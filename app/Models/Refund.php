<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'refund_number',
        'sale_id',
        'customer_id',
        'processed_by',
        'refund_date',
        'subtotal',
        'tax_amount',
        'total_amount',
        'refund_method',
        'status',
        'reason',
        'notes',
    ];

    protected $casts = [
        'refund_date' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Generate unique refund number
     */
    public static function generateRefundNumber(): string
    {
        $prefix = 'RF';
        $date = now()->format('Ymd');
        $lastRefund = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastRefund) {
            $lastNumber = (int) substr($lastRefund->refund_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $date . $newNumber;
    }

    /**
     * Relationships
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function items()
    {
        return $this->hasMany(RefundItem::class);
    }

    public function refundItems()
    {
        return $this->hasMany(RefundItem::class);
    }

    /**
     * Accessors
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending' => 'รอดำเนินการ',
            'approved' => 'อนุมัติแล้ว',
            'completed' => 'เสร็จสิ้น',
            'rejected' => 'ปฏิเสธ',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function getRefundMethodLabelAttribute(): string
    {
        $labels = [
            'cash' => 'เงินสด',
            'transfer' => 'โอนเงิน',
            'credit_card' => 'บัตรเครดิต',
            'store_credit' => 'เครดิตร้าน',
        ];
        return $labels[$this->refund_method] ?? $this->refund_method;
    }
}
