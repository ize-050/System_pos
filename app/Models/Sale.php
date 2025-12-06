<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_number',
        'customer_id',
        'cashier_id',
        'subtotal',
        'discount_amount',
        'shipping_fee',
        'tax_amount',
        'total_amount',
        'payment_method',
        'received_amount',
        'change_amount',
        'status',
        'sale_date',
        'promotion_id',
        'notes',
        'invoice_type',
        'tax_invoice_customer'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'received_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'sale_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'tax_invoice_customer' => 'array'
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('sale_date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query
            ->whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year);
    }

    // Accessors
    public function getFormattedSaleDateAttribute()
    {
        return $this->sale_date->format('d/m/Y H:i:s');
    }

    public function getPaymentMethodLabelAttribute()
    {
        switch ($this->payment_method) {
            case 'cash':
                return 'เงินสด';
            case 'credit':
                return 'บัตรเครดิต';
            case 'customer_account':
                return 'บัญชีลูกค้า';
            default:
                return $this->payment_method;
        }
    }
}
