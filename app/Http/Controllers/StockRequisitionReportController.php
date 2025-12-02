<?php

namespace App\Http\Controllers;

use App\Models\StockRequisition;
use App\Models\StockRequisitionItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockRequisitionReportController extends Controller
{
    /**
     * Display main report page
     */
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // Summary statistics
        $summary = [
            'total_requisitions' => StockRequisition::completed()
                ->dateRange($startDate, $endDate)
                ->count(),
            'total_cost' => StockRequisition::completed()
                ->dateRange($startDate, $endDate)
                ->sum('total_cost_amount'),
            'total_items' => StockRequisition::completed()
                ->dateRange($startDate, $endDate)
                ->sum('total_items'),
            'draft_count' => StockRequisition::draft()
                ->dateRange($startDate, $endDate)
                ->count(),
            'cancelled_count' => StockRequisition::cancelled()
                ->dateRange($startDate, $endDate)
                ->count(),
        ];

        // Top products
        $topProducts = StockRequisitionItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total_cost) as total_cost')
            )
            ->whereHas('requisition', function ($query) use ($startDate, $endDate) {
                $query->where('status', 'completed')
                      ->whereBetween('requisition_date', [$startDate, $endDate]);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->with('product:id,name,sku,unit')
            ->get();

        // Recent requisitions
        $recentRequisitions = StockRequisition::with(['createdBy'])
            ->dateRange($startDate, $endDate)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return Inertia::render('Reports/StockRequisitions/Index', [
            'summary' => $summary,
            'topProducts' => $topProducts,
            'recentRequisitions' => $recentRequisitions,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }

    /**
     * Daily report
     */
    public function daily(Request $request)
    {
        $date = $request->get('date', Carbon::now()->format('Y-m-d'));

        $requisitions = StockRequisition::with(['items.product', 'createdBy'])
            ->whereDate('requisition_date', $date)
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'total_requisitions' => $requisitions->where('status', 'completed')->count(),
            'total_cost' => $requisitions->where('status', 'completed')->sum('total_cost_amount'),
            'total_items' => $requisitions->where('status', 'completed')->sum('total_items'),
            'draft_count' => $requisitions->where('status', 'draft')->count(),
            'cancelled_count' => $requisitions->where('status', 'cancelled')->count(),
        ];

        // Group by department
        $byDepartment = $requisitions->where('status', 'completed')
            ->groupBy('department')
            ->map(function ($items, $department) {
                return [
                    'department' => $department ?: 'ไม่ระบุ',
                    'count' => $items->count(),
                    'total_cost' => $items->sum('total_cost_amount'),
                ];
            })->values();

        return Inertia::render('Reports/StockRequisitions/Daily', [
            'date' => $date,
            'requisitions' => $requisitions,
            'summary' => $summary,
            'byDepartment' => $byDepartment,
        ]);
    }

    /**
     * Monthly report
     */
    public function monthly(Request $request)
    {
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        // Daily breakdown
        $dailyData = StockRequisition::select(
                DB::raw('DATE(requisition_date) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_cost_amount) as total_cost'),
                DB::raw('SUM(total_items) as total_items')
            )
            ->where('status', 'completed')
            ->whereBetween('requisition_date', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(requisition_date)'))
            ->orderBy('date')
            ->get();

        // Summary
        $summary = [
            'total_requisitions' => StockRequisition::completed()
                ->dateRange($startDate, $endDate)
                ->count(),
            'total_cost' => StockRequisition::completed()
                ->dateRange($startDate, $endDate)
                ->sum('total_cost_amount'),
            'total_items' => StockRequisition::completed()
                ->dateRange($startDate, $endDate)
                ->sum('total_items'),
            'avg_per_day' => $dailyData->count() > 0 
                ? round($dailyData->sum('total_cost') / $dailyData->count(), 2) 
                : 0,
        ];

        // Top products
        $topProducts = StockRequisitionItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total_cost) as total_cost')
            )
            ->whereHas('requisition', function ($query) use ($startDate, $endDate) {
                $query->where('status', 'completed')
                      ->whereBetween('requisition_date', [$startDate, $endDate]);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->with('product:id,name,sku,unit')
            ->get();

        // By department
        $byDepartment = StockRequisition::select(
                'department',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_cost_amount) as total_cost')
            )
            ->where('status', 'completed')
            ->whereBetween('requisition_date', [$startDate, $endDate])
            ->groupBy('department')
            ->orderByDesc('total_cost')
            ->get();

        return Inertia::render('Reports/StockRequisitions/Monthly', [
            'month' => $month,
            'dailyData' => $dailyData,
            'summary' => $summary,
            'topProducts' => $topProducts,
            'byDepartment' => $byDepartment,
        ]);
    }

    /**
     * Yearly report
     */
    public function yearly(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $startDate = Carbon::create($year, 1, 1)->startOfYear();
        $endDate = Carbon::create($year, 12, 31)->endOfYear();

        // Monthly breakdown
        $monthlyData = StockRequisition::select(
                DB::raw('MONTH(requisition_date) as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_cost_amount) as total_cost'),
                DB::raw('SUM(total_items) as total_items')
            )
            ->where('status', 'completed')
            ->whereBetween('requisition_date', [$startDate, $endDate])
            ->groupBy(DB::raw('MONTH(requisition_date)'))
            ->orderBy('month')
            ->get();

        // Summary
        $summary = [
            'total_requisitions' => StockRequisition::completed()
                ->dateRange($startDate, $endDate)
                ->count(),
            'total_cost' => StockRequisition::completed()
                ->dateRange($startDate, $endDate)
                ->sum('total_cost_amount'),
            'total_items' => StockRequisition::completed()
                ->dateRange($startDate, $endDate)
                ->sum('total_items'),
            'avg_per_month' => $monthlyData->count() > 0 
                ? round($monthlyData->sum('total_cost') / $monthlyData->count(), 2) 
                : 0,
        ];

        // Top products
        $topProducts = StockRequisitionItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total_cost) as total_cost')
            )
            ->whereHas('requisition', function ($query) use ($startDate, $endDate) {
                $query->where('status', 'completed')
                      ->whereBetween('requisition_date', [$startDate, $endDate]);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->with('product:id,name,sku,unit')
            ->get();

        // Compare with last year
        $lastYearStart = Carbon::create($year - 1, 1, 1)->startOfYear();
        $lastYearEnd = Carbon::create($year - 1, 12, 31)->endOfYear();
        
        $lastYearTotal = StockRequisition::completed()
            ->dateRange($lastYearStart, $lastYearEnd)
            ->sum('total_cost_amount');

        $comparison = [
            'last_year_total' => $lastYearTotal,
            'difference' => $summary['total_cost'] - $lastYearTotal,
            'percentage' => $lastYearTotal > 0 
                ? round((($summary['total_cost'] - $lastYearTotal) / $lastYearTotal) * 100, 2) 
                : 0,
        ];

        return Inertia::render('Reports/StockRequisitions/Yearly', [
            'year' => $year,
            'monthlyData' => $monthlyData,
            'summary' => $summary,
            'topProducts' => $topProducts,
            'comparison' => $comparison,
        ]);
    }

    /**
     * Export report
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'daily');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $requisitions = StockRequisition::with(['items.product', 'createdBy'])
            ->where('status', 'completed')
            ->whereBetween('requisition_date', [$startDate, $endDate])
            ->orderBy('requisition_date')
            ->get();

        // For now, return JSON. Can implement Excel export later
        return response()->json([
            'success' => true,
            'data' => $requisitions,
            'summary' => [
                'total_requisitions' => $requisitions->count(),
                'total_cost' => $requisitions->sum('total_cost_amount'),
                'total_items' => $requisitions->sum('total_items'),
            ],
        ]);
    }
}
