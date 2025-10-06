<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Services\ExportService;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PurchaseReportController extends Controller
{
    protected $reportService;
    protected $exportService;

    public function __construct(ReportService $reportService, ExportService $exportService)
    {
        $this->reportService = $reportService;
        $this->exportService = $exportService;
        
        // Only admin, manager, and accountant can access
        $this->middleware(['auth', 'role:admin|manager|accountant']);
    }

    /**
     * Display purchase report page
     */
    public function index(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'supplier_id', 'status', 'per_page']);
        
        $reportData = $this->reportService->getPurchaseReport($filters);
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Reports/PurchaseReport', [
            'purchaseOrders' => $reportData['purchaseOrders'],
            'summary' => $reportData['summary'],
            'suppliers' => $suppliers,
            'filters' => $filters,
        ]);
    }

    /**
     * Export purchase report
     */
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:xlsx,pdf',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        try {
            $filters = $request->only(['start_date', 'end_date', 'supplier_id', 'status']);
            $reportData = $this->reportService->getPurchaseReport($filters);

            $result = $this->exportService->exportToExcel(
                'purchase',
                $reportData['purchaseOrders'],
                $filters,
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'กำลังสร้างไฟล์ Export',
                'download_url' => $result['path'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
