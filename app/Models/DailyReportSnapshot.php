<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReportSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_date',
        'sales_total',
        'purchase_total',
        'profit_loss',
        'transaction_count',
        'generated_at',
    ];

    protected $casts = [
        'report_date' => 'date',
        'sales_total' => 'decimal:2',
        'purchase_total' => 'decimal:2',
        'profit_loss' => 'decimal:2',
        'generated_at' => 'datetime',
    ];

    /**
     * Get the profit margin percentage
     */
    public function getProfitMarginAttribute()
    {
        if ($this->sales_total == 0) {
            return 0;
        }
        
        return ($this->profit_loss / $this->sales_total) * 100;
    }

    /**
     * Scope to get reports for a specific date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('report_date', [$startDate, $endDate]);
    }

    /**
     * Scope to get recent reports
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('report_date', '>=', now()->subDays($days))
                     ->orderBy('report_date', 'desc');
    }
}
