<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\User;
use App\Models\ProductCategory;
use App\Models\Stock;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display the dashboard with statistics and reports
     */
    public function index(Request $request)
    {
        // Check authorization
        if (!in_array(auth()->user()->role, ['admin', 'manager', 'cashier'])) {
            abort(403, 'Unauthorized');
        }

        // Get date range from request or default to current month
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Basic statistics
        $totalSales = Sale::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $totalOrders = Sale::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalProducts = Product::where('is_active', true)->count();
        $lowStockProducts = Product::whereRaw('current_stock <= reorder_point')->count();

        // Sales trend (last 7 days)
        $salesTrend = Sale::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as total'),
            DB::raw('COUNT(*) as orders')
        )
        ->whereBetween('created_at', [Carbon::now()->subDays(6), Carbon::now()])
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // Top selling products
        $topProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->select(
                'products.name',
                'products.image_path',
                DB::raw('SUM(sale_items.quantity) as total_sold'),
                DB::raw('SUM(sale_items.total_price) as total_revenue')
            )
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name', 'products.image_path')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        // Sales by payment method
        $paymentMethods = Sale::select(
            'payment_method',
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_amount) as total')
        )
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('payment_method')
        ->get();

        // Recent sales
        $recentSales = Sale::with(['cashier', 'saleItems.product'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Low stock alerts
        $lowStockItems = Product::with('category')
            ->whereRaw('current_stock <= reorder_point')
            ->limit(10)
            ->get();

        // Monthly comparison
        $currentMonth = Sale::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->sum('total_amount');

        $lastMonth = Sale::whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth()
        ])->sum('total_amount');

        $monthlyGrowth = $lastMonth > 0 ? (($currentMonth - $lastMonth) / $lastMonth) * 100 : 0;

        return Inertia::render('Dashboard', [
            'statistics' => [
                'total_sales' => $totalSales,
                'total_orders' => $totalOrders,
                'total_products' => $totalProducts,
                'low_stock_products' => $lowStockProducts,
                'monthly_growth' => round($monthlyGrowth, 2),
            ],
            'sales_trend' => $salesTrend,
            'top_products' => $topProducts,
            'payment_methods' => $paymentMethods,
            'recent_sales' => $recentSales,
            'low_stock_items' => $lowStockItems,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }

    /**
     * Get sales report data
     */
    public function salesReport(Request $request)
    {
        // Check authorization
        if (!in_array(auth()->user()->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized');
        }

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());
        $groupBy = $request->get('group_by', 'day'); // day, week, month

        // Convert to Carbon for proper date range (include full end date)
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        $query = Sale::select(
            DB::raw($this->getDateGrouping($groupBy) . ' as period'),
            DB::raw('SUM(total_amount) as total_sales'),
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('AVG(total_amount) as average_order')
        )
        ->whereBetween('created_at', [$startDateTime, $endDateTime])
        ->groupBy('period')
        ->orderBy('period');

        $salesData = $query->get();

        return Inertia::render('Reports/Sales', [
            'sales_data' => $salesData,
            'summary' => [
                'total_sales' => $salesData->sum('total_sales'),
                'total_orders' => $salesData->sum('total_orders'),
                'average_order' => $salesData->avg('average_order'),
            ],
            'filters' => $request->only(['start_date', 'end_date', 'group_by'])
        ]);
    }

    /**
     * Get product performance report
     */
    public function productReport(Request $request)
    {
        // Check authorization
        if (!in_array(auth()->user()->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized');
        }

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());
        $limit = $request->get('limit', 20);

        // Convert to Carbon for proper date range (include full end date)
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        $productData = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'products.id',
                'products.name',
                'products.image_path',
                'categories.name as category_name',
                DB::raw('SUM(sale_items.quantity) as total_sold'),
                DB::raw('SUM(sale_items.total_price) as total_revenue'),
                DB::raw('AVG(sale_items.unit_price) as average_price'),
                DB::raw('COUNT(DISTINCT sales.id) as orders_count')
            )
            ->whereBetween('sales.created_at', [$startDateTime, $endDateTime])
            ->groupBy('products.id', 'products.name', 'products.image_path', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'product_data' => $productData
        ]);
    }

    /**
     * Get inventory report
     */
    public function inventoryReport(Request $request)
    {
        // Check authorization
        if (!in_array(auth()->user()->role, ['admin', 'manager', 'accountant'])) {
            abort(403, 'Unauthorized');
        }

        $categoryId = $request->get('category_id');
        $status = $request->get('status', 'all'); // all, low_stock, out_of_stock

        $query = Product::with('category')
            ->select('products.*');

        if ($categoryId) {
            $query->where('products.category_id', $categoryId);
        }

        if ($status === 'low_stock') {
            $query->whereRaw('current_stock <= reorder_point')->where('current_stock', '>', 0);
        } elseif ($status === 'out_of_stock') {
            $query->where('current_stock', '<=', 0);
        }

        $inventoryData = $query->orderBy('current_stock', 'asc')->get();

        // Get categories for filter
        $categories = ProductCategory::orderBy('name')->get();

        // Calculate summary
        $totalProducts = Product::count();
        $lowStockProducts = Product::whereRaw('current_stock <= reorder_point')->where('current_stock', '>', 0)->count();
        $outOfStockProducts = Product::where('current_stock', '<=', 0)->count();
        $totalStockValue = Product::selectRaw('SUM(current_stock * cost_price) as total_value')->value('total_value');

        return Inertia::render('Reports/Inventory', [
            'inventoryData' => $inventoryData,
            'categories' => $categories,
            'filters' => [
                'category_id' => $categoryId,
                'status' => $status,
            ],
            'summary' => [
                'total_products' => $totalProducts,
                'low_stock_products' => $lowStockProducts,
                'out_of_stock_products' => $outOfStockProducts,
                'total_stock_value' => $totalStockValue ?? 0,
            ],
        ]);
    }

    /**
     * Get comprehensive analytics data
     */
    public function comprehensiveAnalytics(Request $request)
    {
        // Check authorization
        if (!in_array(auth()->user()->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized');
        }

        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $analysisType = $request->input('analysis_type', 'daily');

        // Basic metrics
        $totalSales = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        $totalOrders = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // Calculate gross profit
        $grossProfit = 0;
        $sales = Sale::with('saleItems.product')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        foreach ($sales as $sale) {
            foreach ($sale->saleItems as $item) {
                $profit = ($item->price - ($item->product->cost_price ?? 0)) * $item->quantity;
                $grossProfit += $profit;
            }
        }

        // Customer analysis
        $totalCustomers = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->distinct('customer_id')
            ->whereNotNull('customer_id')
            ->count();

        // Customer segmentation (simplified) - Removed VIP customers
        $regularCustomers = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('customer_id, SUM(total_amount) as total_spent')
            ->whereNotNull('customer_id')
            ->groupBy('customer_id')
            ->having('total_spent', '>', 1000)
            ->count();

        $newCustomers = $totalCustomers - $regularCustomers;

        // Top selling products
        $topProducts = SaleItem::join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->selectRaw('
                products.name,
                products.cost_price,
                SUM(sale_items.quantity) as total_quantity,
                SUM(sale_items.unit_price * sale_items.quantity) as total_revenue,
                SUM((sale_items.unit_price - products.cost_price) * sale_items.quantity) as total_profit
            ')
            ->groupBy('products.id', 'products.name', 'products.cost_price')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        // Slow moving products
        $slowProducts = SaleItem::join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->selectRaw('
                products.name,
                products.cost_price,
                SUM(sale_items.quantity) as total_quantity,
                SUM(sale_items.unit_price * sale_items.quantity) as total_revenue,
                SUM((sale_items.unit_price - products.cost_price) * sale_items.quantity) as total_profit
            ')
            ->groupBy('products.id', 'products.name', 'products.cost_price')
            ->orderBy('total_quantity')
            ->limit(10)
            ->get();

        // Sales trend data
        $salesTrend = $this->getSalesTrendData($startDate, $endDate, $analysisType);

        // Top customers
        $topCustomers = Sale::join('customers', 'sales.customer_id', '=', 'customers.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->whereNotNull('sales.customer_id')
            ->selectRaw('
                customers.id,
                customers.name,
                customers.phone,
                SUM(sales.total_amount) as total_spent,
                COUNT(sales.id) as total_orders,
                AVG(sales.total_amount) as avg_order_value,
                MAX(sales.created_at) as last_purchase
            ')
            ->groupBy('customers.id', 'customers.name', 'customers.phone')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        return response()->json([
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'gross_profit' => $grossProfit,
            'profit_margin' => $totalSales > 0 ? round(($grossProfit / $totalSales) * 100, 2) : 0,
            'total_customers' => $totalCustomers,
            'average_order_value' => $averageOrderValue,
            'avg_order_value' => $averageOrderValue,
            'regular_customers' => $regularCustomers,
            'new_customers' => $newCustomers,
            'top_products' => $topProducts,
            'slow_products' => $slowProducts,
            'sales_trend' => $salesTrend,
            'top_customers' => $topCustomers
        ]);
    }

    /**
     * Get sales trend data based on analysis type
     */
    private function getSalesTrendData($startDate, $endDate, $analysisType)
    {
        $dateFormat = '%Y-%m-%d'; // default to daily
        
        switch($analysisType) {
            case 'daily':
                $dateFormat = '%Y-%m-%d';
                break;
            case 'weekly':
                $dateFormat = '%Y-%u';
                break;
            case 'monthly':
                $dateFormat = '%Y-%m';
                break;
            case 'yearly':
                $dateFormat = '%Y';
                break;
        }

        return DB::table('sales')
            ->leftJoin('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->leftJoin('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                DB::raw("DATE_FORMAT(sales.created_at, '{$dateFormat}') as period"),
                DB::raw('SUM(sales.total_amount) as total_sales'),
                DB::raw('SUM((sale_items.unit_price - COALESCE(products.cost_price, 0)) * sale_items.quantity) as gross_profit'),
                DB::raw('COUNT(DISTINCT sales.id) as total_orders')
            )
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->groupBy('period')
            ->orderBy('period')
            ->get();
    }

    /**
     * Export analytics report
     */
    public function exportAnalytics(Request $request)
    {
        // Check authorization
        if (!in_array(auth()->user()->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized');
        }

        $format = $request->input('format', 'pdf'); // pdf or excel
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $analysisType = $request->input('analysis_type', 'daily');

        // Get analytics data
        $analyticsRequest = new Request([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'analysis_type' => $analysisType
        ]);
        
        $analyticsResponse = $this->comprehensiveAnalytics($analyticsRequest);
        $analytics = json_decode($analyticsResponse->getContent(), true);

        if ($format === 'excel') {
            // For now, return JSON data that can be processed by frontend
            return response()->json([
                'message' => 'Excel export feature will be implemented with frontend processing',
                'data' => $analytics
            ]);
        } else {
            // PDF export - simple HTML to PDF conversion
            $html = $this->generateAnalyticsHTML($analytics, $startDate, $endDate, $analysisType);
            
            return response()->json([
                'message' => 'PDF export feature will be implemented with frontend processing',
                'html' => $html,
                'data' => $analytics
            ]);
        }
    }

    private function generateAnalyticsHTML($analytics, $startDate, $endDate, $analysisType)
    {
        $html = "
        <html>
        <head>
            <title>รายงานการวิเคราะห์เชิงลึก</title>
            <style>
                body { font-family: 'Sarabun', sans-serif; }
                .header { text-align: center; margin-bottom: 20px; }
                .metrics { display: flex; justify-content: space-around; margin: 20px 0; }
                .metric { text-align: center; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>รายงานการวิเคราะห์เชิงลึก</h1>
                <p>ช่วงเวลา: {$startDate} ถึง {$endDate}</p>
                <p>ประเภทการวิเคราะห์: {$analysisType}</p>
            </div>
            
            <div class='metrics'>
                <div class='metric'>
                    <h3>ยอดขายรวม</h3>
                    <p>" . number_format($analytics['total_sales'], 2) . " บาท</p>
                </div>
                <div class='metric'>
                    <h3>กำไรขั้นต้น</h3>
                    <p>" . number_format($analytics['gross_profit'], 2) . " บาท</p>
                </div>
                <div class='metric'>
                    <h3>ลูกค้าทั้งหมด</h3>
                    <p>" . number_format($analytics['total_customers']) . " คน</p>
                </div>
                <div class='metric'>
                    <h3>ค่าเฉลี่ยต่อออเดอร์</h3>
                    <p>" . number_format($analytics['average_order_value'], 2) . " บาท</p>
                </div>
            </div>
            
            <h2>สินค้าขายดี Top 10</h2>
            <table>
                <tr>
                    <th>ชื่อสินค้า</th>
                    <th>จำนวนขาย</th>
                    <th>รายได้</th>
                    <th>กำไร</th>
                </tr>";
        
        foreach ($analytics['top_products'] as $product) {
            $html .= "
                <tr>
                    <td>{$product['name']}</td>
                    <td>" . number_format($product['total_quantity']) . "</td>
                    <td>" . number_format($product['total_revenue'], 2) . " บาท</td>
                    <td>" . number_format($product['total_profit'], 2) . " บาท</td>
                </tr>";
        }
        
        $html .= "
            </table>
        </body>
        </html>";
        
        return $html;
    }

    /**
     * Helper method to get date grouping SQL
     */
    private function getDateGrouping($groupBy)
    {
        switch ($groupBy) {
            case 'week':
                return "DATE_FORMAT(created_at, '%Y-%u')";
            case 'month':
                return "DATE_FORMAT(created_at, '%Y-%m')";
            case 'day':
            default:
                return "DATE(created_at)";
        }
    }
}
