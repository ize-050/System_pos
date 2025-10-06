<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'value',
        'min_quantity',
        'free_quantity',
        'min_amount',
        'max_discount',
        'start_date',
        'end_date',
        'is_active',
        'applicable_products',
        'applicable_categories',
        'usage_limit',
        'used_count'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'applicable_products' => 'array',
        'applicable_categories' => 'array',
        'value' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_discount' => 'decimal:2'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where(function ($q) use ($productId) {
            $q->whereNull('applicable_products')
              ->orWhereJsonContains('applicable_products', $productId);
        });
    }

    public function scopeForCategory($query, $categoryId)
    {
        return $query->where(function ($q) use ($categoryId) {
            $q->whereNull('applicable_categories')
              ->orWhereJsonContains('applicable_categories', $categoryId);
        });
    }

    // Accessors & Mutators
    public function getTypeLabelAttribute()
    {
        switch($this->type) {
            case 'percentage':
                return 'ส่วนลดเปอร์เซ็นต์';
            case 'fixed_amount':
                return 'ส่วนลดจำนวนเงิน';
            case 'buy_x_get_y':
                return 'ซื้อ X ได้ Y';
            default:
                return $this->type;
        }
    }

    // Helper Methods
    public function isValid(): bool
    {
        return $this->is_active 
            && $this->start_date <= now() 
            && $this->end_date >= now()
            && ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }

    public function canApplyToProduct($productId): bool
    {
        if ($this->applicable_products === null) {
            return true;
        }
        
        $products = is_array($this->applicable_products) 
            ? $this->applicable_products 
            : json_decode($this->applicable_products, true) ?? [];
            
        return in_array($productId, $products);
    }

    public function canApplyToCategory($categoryId): bool
    {
        if ($this->applicable_categories === null) {
            return true;
        }
        
        $categories = is_array($this->applicable_categories) 
            ? $this->applicable_categories 
            : json_decode($this->applicable_categories, true) ?? [];
            
        return in_array($categoryId, $categories);
    }

    public function calculateDiscount($amount, $quantity = 1): float
    {
        if (!$this->isValid()) {
            return 0;
        }

        // ตรวจสอบยอดขั้นต่ำ
        if ($this->min_amount && $amount < $this->min_amount) {
            return 0;
        }

        $discount = 0;

        switch ($this->type) {
            case 'percentage':
                $discount = $amount * ($this->value / 100);
                break;
            
            case 'fixed_amount':
                $discount = $this->value;
                break;
            
            case 'buy_x_get_y':
                if ($this->min_quantity && $quantity >= $this->min_quantity) {
                    $freeItems = floor($quantity / $this->min_quantity) * $this->free_quantity;
                    $discount = $freeItems * ($amount / $quantity); // ราคาต่อชิ้น * จำนวนที่ได้ฟรี
                }
                break;
        }

        // ตรวจสอบส่วนลดสูงสุด
        if ($this->max_discount && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }

        return round($discount, 2);
    }

    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    // Note: This model uses JSON fields (applicable_products, applicable_categories) 
    // instead of pivot tables for product/category relationships
}
