<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'report_type',
        'file_format',
        'status',
        'file_path',
        'filters_applied',
        'error_message',
    ];

    protected $casts = [
        'filters_applied' => 'array',
    ];

    /**
     * Get the user that owns the export job
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get jobs by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get pending jobs
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get completed jobs
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get failed jobs
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Mark job as processing
     */
    public function markAsProcessing()
    {
        $this->update(['status' => 'processing']);
    }

    /**
     * Mark job as completed
     */
    public function markAsCompleted($filePath)
    {
        $this->update([
            'status' => 'completed',
            'file_path' => $filePath,
        ]);
    }

    /**
     * Mark job as failed
     */
    public function markAsFailed($errorMessage)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Check if job is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if job is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if job is processing
     */
    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    /**
     * Check if job has failed
     */
    public function hasFailed()
    {
        return $this->status === 'failed';
    }
}
