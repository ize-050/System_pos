<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_code',
        'name', // ชื่อลูกค้า
        'phone', // เบอร์โทร
        'line_id', // Line ID
        'email',
        'address',
        'company_name',
        'tax_id',
        'credit_limit',
        'current_balance',
        'payment_terms',
        'customer_type',
        'is_active',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('customer_type', $type);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('customer_code', 'like', "%{$search}%")
              ->orWhere('name', 'like', "%{$search}%")
              ->orWhere('company_name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('line_id', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->name ?: $this->company_name;
    }

    public function getPurchaseHistoryAttribute()
    {
        return $this->sales()->with('saleItems.product')->orderBy('created_at', 'desc')->get();
    }

    public function getAvailableCreditAttribute()
    {
        return $this->credit_limit - $this->current_balance;
    }
}
