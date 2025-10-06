<?php

namespace App\Http\Controllers;

use App\Models\DailyReportSnapshot;
use App\Services\ReportService;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DailyReportController extends Controller
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
     * Display list of daily reports
     */
    public function index(Request $request)
    {
        $reports = DailyReportSnapshot::orderBy('report_date', 'desc')
            ->paginate(30);

        return Inertia::render('Reports/DailyReports', [
            'reports' => $reports,
        ]);
    }

    /**
     * Show specific daily report
     */
    public function show($date)
    {
        $report = DailyReportSnapshot::where('report_date', $date)->firstOrFail();

        return Inertia::render('Reports/DailyReportView', [
            'report' => $report,
        ]);
    }

    /**
     * Generate daily report manually
     */
    public function generate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        try {
            $date = $request->input('date');
            $report = $this->reportService->generateDailyReport($date);

            return response()->json([
                'success' => true,
                'message' => 'สร้างรายงานประจำวันสำเร็จ',
                'report' => $report,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Export daily report
     */
    public function export(Request $request)
    {
        $request->validate([
            'report_date' => 'required|date',
            'format' => 'required|in:xlsx,pdf',
        ]);

        try {
            $date = $request->input('report_date');
            $format = $request->input('format');
            
            $report = DailyReportSnapshot::where('report_date', $date)->firstOrFail();

            if ($format === 'xlsx') {
                // Export to Excel
                $result = $this->exportService->exportToExcel(
                    'daily_report',
                    [$report],
                    ['report_date' => $date],
                    auth()->id()
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Export สำเร็จ',
                    'download_url' => $result['path'],
                ]);
            } else {
                // Export to PDF (placeholder)
                return response()->json([
                    'success' => true,
                    'message' => 'PDF export will be implemented',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
