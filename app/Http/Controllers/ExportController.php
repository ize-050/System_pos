<?php

namespace App\Http\Controllers;

use App\Services\ExportService;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    protected $exportService;
    protected $reportService;

    public function __construct(ExportService $exportService, ReportService $reportService)
    {
        $this->exportService = $exportService;
        $this->reportService = $reportService;
        
        // Only admin, manager, and accountant can export
        $this->middleware(['auth', 'role:admin|manager|accountant']);
    }

    /**
     * Export report to Excel
     */
    public function export(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:purchase,sales,inventory,product',
            'format' => 'required|in:xlsx,pdf',
            'filters' => 'nullable|array',
        ]);

        try {
            $reportType = $request->input('report_type');
            $filters = $request->input('filters', []);

            // Get report data based on type
            $data = $this->getReportData($reportType, $filters);

            // Export to Excel
            $result = $this->exportService->exportToExcel(
                $reportType,
                $data,
                $filters,
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Export สำเร็จ',
                'download_url' => $result['path'],
                'filename' => $result['filename'],
                'export_job_id' => $result['export_job_id'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get report data based on type
     */
    private function getReportData($reportType, $filters)
    {
        switch ($reportType) {
            case 'purchase':
                $reportData = $this->reportService->getPurchaseReport($filters);
                return $reportData['purchaseOrders'];
            
            case 'sales':
                $reportData = $this->reportService->getSalesReport($filters);
                return $reportData['sales'];
            
            case 'inventory':
                $reportData = $this->reportService->getInventoryReport($filters);
                return $reportData['products'];
            
            case 'product':
                $reportData = $this->reportService->getInventoryReport($filters);
                return $reportData['products'];
            
            default:
                throw new \Exception('Invalid report type');
        }
    }

    /**
     * Check export job status
     */
    public function status($jobId)
    {
        try {
            $status = $this->exportService->getExportJobStatus($jobId);

            return response()->json([
                'success' => true,
                'status' => $status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Download export file
     */
    public function download($jobId)
    {
        try {
            return $this->exportService->downloadExport($jobId);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}
