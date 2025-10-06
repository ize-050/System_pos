<?php

namespace App\Services;

use App\Models\ExportJob;
use App\Exports\PurchaseReportExport;
use App\Exports\SalesReportExport;
use App\Exports\InventoryReportExport;
use App\Exports\ProductReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExportService
{
    const MAX_RECORDS = 50000;

    /**
     * Export report to Excel
     */
    public function exportToExcel($reportType, $data, $filters = [], $userId = null)
    {
        // Check record limit
        if ($this->exceedsRecordLimit($data)) {
            throw new \Exception('ข้อมูลเกิน 50,000 รายการ กรุณากรองข้อมูลหรือแบ่งการ export');
        }

        // Create export job
        $exportJob = $this->createExportJob($userId, $reportType, 'xlsx', $filters);

        try {
            $exportJob->markAsProcessing();

            // Generate filename
            $filename = $this->generateFilename($reportType, $filters);

            // Select appropriate export class
            $exportClass = $this->getExportClass($reportType, $data);

            // Export to file
            $filePath = 'exports/' . $filename;
            Excel::store($exportClass, $filePath, 'public');

            $exportJob->markAsCompleted($filePath);

            return [
                'success' => true,
                'filename' => $filename,
                'path' => Storage::url($filePath),
                'export_job_id' => $exportJob->id,
            ];
        } catch (\Exception $e) {
            $exportJob->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if data exceeds record limit
     */
    private function exceedsRecordLimit($data)
    {
        if (is_array($data)) {
            return count($data) > self::MAX_RECORDS;
        }

        if (is_object($data) && method_exists($data, 'count')) {
            return $data->count() > self::MAX_RECORDS;
        }

        return false;
    }

    /**
     * Create export job record
     */
    private function createExportJob($userId, $reportType, $fileFormat, $filters)
    {
        return ExportJob::create([
            'user_id' => $userId,
            'report_type' => $reportType,
            'file_format' => $fileFormat,
            'status' => 'pending',
            'filters_applied' => $filters,
        ]);
    }

    /**
     * Generate filename for export
     */
    private function generateFilename($reportType, $filters)
    {
        $reportName = ucfirst($reportType) . 'Report';
        $timestamp = Carbon::now()->format('Ymd_His');

        $dateRange = '';
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $dateRange = '_' . $filters['start_date'] . '_to_' . $filters['end_date'];
        }

        return $reportName . $dateRange . '_' . $timestamp . '.xlsx';
    }

    /**
     * Get appropriate export class
     */
    private function getExportClass($reportType, $data)
    {
        switch ($reportType) {
            case 'purchase':
                return new PurchaseReportExport($data);
            case 'sales':
                return new SalesReportExport($data);
            case 'inventory':
                return new InventoryReportExport($data);
            case 'product':
                return new ProductReportExport($data);
            default:
                throw new \Exception('Invalid report type');
        }
    }

    /**
     * Format currency for Thai Baht
     */
    public function formatCurrency($amount)
    {
        return '฿' . number_format($amount, 2);
    }

    /**
     * Get export job status
     */
    public function getExportJobStatus($jobId)
    {
        $job = ExportJob::findOrFail($jobId);

        return [
            'status' => $job->status,
            'file_path' => $job->file_path,
            'error_message' => $job->error_message,
            'created_at' => $job->created_at,
        ];
    }

    /**
     * Download export file
     */
    public function downloadExport($jobId)
    {
        $job = ExportJob::findOrFail($jobId);

        if (!$job->isCompleted()) {
            throw new \Exception('Export is not completed yet');
        }

        if (!Storage::disk('public')->exists($job->file_path)) {
            throw new \Exception('Export file not found');
        }

        return Storage::disk('public')->download($job->file_path);
    }

    /**
     * Clean up old export files
     */
    public function cleanupOldExports($daysOld = 7)
    {
        $cutoffDate = Carbon::now()->subDays($daysOld);

        $oldJobs = ExportJob::where('created_at', '<', $cutoffDate)
            ->where('status', 'completed')
            ->get();

        foreach ($oldJobs as $job) {
            if ($job->file_path && Storage::disk('public')->exists($job->file_path)) {
                Storage::disk('public')->delete($job->file_path);
            }
            $job->delete();
        }

        return $oldJobs->count();
    }
}
