<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\DailyReportSnapshot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ReportService
{
    /**
     * Get purchase report data
     */
    public function getPurchaseReport($filters = [])
    {
        $query = PurchaseOrder::with(['supplier', 'items.product'])
            ->where('status', 'received');

        // Apply filters
        if (!empty($filters['start_date'])) {
            $query->where('order_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->where('order_date', '<=', $filters['end_date']);
        }

        if (!empty($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Pagination
        $perPage = $filters['per_page'] ?? 50;
        $purchaseOrders = $query->orderBy('order_date', 'desc')->paginate($perPage);

        // Calculate summary
        $summary = $this->calculatePurchaseSummary($query->get());

        return [
            'purchaseOrders' => $purchaseOrders,
            'summary' => $summary,
        ];
    }

    /**
     * Calculate purchase summary statistics
     */
    private function calculatePurchaseSummary($purchaseOrders)
    {
        $totalPOs = $purchaseOrders->count();
        $totalSubtotal = $purchaseOrders->sum('subtotal');
        $totalVat = $purchaseOrders->sum('vat_amount');
        $grandTotal = $purchaseOrders->sum('total_amount');
        $averageOrderValue = $totalPOs > 0 ? $totalSubtotal / $totalPOs : 0;

        return [
            'total_pos' => $totalPOs,
            'total_subtotal' => $totalSubtotal,
            'total_vat' => $totalVat,
            'grand_total' => $grandTotal,
            'average_order_value' => $averageOrderValue,
        ];
    }

    /**
     * Get dashboard data (with caching)
     */
    public function getDashboardData($period = 30)
    {
        return Cache::remember("dashboard_data_{$period}", 300, function () use ($period) {
            return [
                'todaysSummary' => $this->getTodaysSummary(),
                'chartData' => $this->getChartData($period),
                'topProducts' => $this->getTopProducts(),
                'lowStockAlerts' => $this->getLowStockAlerts(),
                'profitLoss' => $this->getProfitLoss(),
            ];
        });
    }

    /**
     * Get today's summary
     */
    private function getTodaysSummary()
    {
        $today = Carbon::today()->toDateString();

        $salesToday = Sale::whereDate('sale_date', $today)->get();
        $purchasesToday = PurchaseOrder::whereDate('order_date', $today)
            ->where('status', 'received')
            ->get();

        return [
            'sales_total' => $salesToday->sum('total_amount'),
            'purchase_total' => $purchasesToday->sum('total_amount'),
            'transaction_count' => $salesToday->count(),
            'customers_served' => $salesToday->pluck('customer_id')->unique()->count(),
        ];
    }

    /**
     * Get chart data for sales vs purchases
     */
    private function getChartData($period)
    {
        $dates = [];
        $sales = [];
        $purchases = [];

        for ($i = $period - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->toDateString();
            $dates[] = $date;

            $salesTotal = Sale::whereDate('sale_date', $date)->sum('total_amount');
            $purchasesTotal = PurchaseOrder::whereDate('order_date', $date)
                ->where('status', 'received')
                ->sum('total_amount');

            $sales[] = $salesTotal;
            $purchases[] = $purchasesTotal;
        }

        return [
            'dates' => $dates,
            'sales' => $sales,
            'purchases' => $purchases,
        ];
    }

    /**
     * Get top 10 best-selling products
     */
    private function getTopProducts($limit = 10)
    {
        return DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'products.id as product_id',
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue'),
                'products.current_stock'
            )
            ->groupBy('products.id', 'products.name', 'products.current_stock')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get low stock alerts
     */
    private function getLowStockAlerts()
    {
        return Product::whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('is_active', true)
            ->select('id', 'name', 'sku', 'current_stock', 'minimum_stock')
            ->orderBy('current_stock', 'asc')
            ->get()
            ->map(function ($product) {
                $product->reorder_quantity = max($product->minimum_stock * 2 - $product->current_stock, 0);
                return $product;
            });
    }

    /**
     * Get profit/loss summary
     */
    private function getProfitLoss()
    {
        $sales = Sale::all();
        
        $totalRevenue = $sales->sum('total_amount');
        $totalCOGS = $sales->sum('cost_of_goods');
        $grossProfit = $totalRevenue - $totalCOGS;
        $profitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        return [
            'total_revenue' => $totalRevenue,
            'total_cogs' => $totalCOGS,
            'gross_profit' => $grossProfit,
            'profit_margin_percentage' => round($profitMargin, 2),
        ];
    }

    /**
     * Generate daily report snapshot
     */
    public function generateDailyReport($date = null)
    {
        $date = $date ?? Carbon::yesterday()->toDateString();

        $salesTotal = Sale::whereDate('sale_date', $date)->sum('total_amount');
        $purchaseTotal = PurchaseOrder::whereDate('order_date', $date)
            ->where('status', 'received')
            ->sum('total_amount');
        $transactionCount = Sale::whereDate('sale_date', $date)->count();
        $profitLoss = $salesTotal - $purchaseTotal;

        return DailyReportSnapshot::updateOrCreate(
            ['report_date' => $date],
            [
                'sales_total' => $salesTotal,
                'purchase_total' => $purchaseTotal,
                'profit_loss' => $profitLoss,
                'transaction_count' => $transactionCount,
                'generated_at' => now(),
            ]
        );
    }

    /**
     * Get sales report data
     */
    public function getSalesReport($filters = [])
    {
        $query = Sale::with(['customer', 'items.product', 'user']);

        // Apply filters
        if (!empty($filters['start_date'])) {
            $query->where('sale_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->where('sale_date', '<=', $filters['end_date']);
        }

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        // Pagination
        $perPage = $filters['per_page'] ?? 50;
        $sales = $query->orderBy('sale_date', 'desc')->paginate($perPage);

        // Calculate summary
        $summary = [
            'total_sales' => $query->count(),
            'total_revenue' => $query->sum('total_amount'),
            'total_discount' => $query->sum('discount_amount'),
            'average_sale_value' => $query->avg('total_amount'),
        ];

        return [
            'sales' => $sales,
            'summary' => $summary,
        ];
    }

    /**
     * Get inventory report data
     */
    public function getInventoryReport($filters = [])
    {
        $query = Product::with('category');

        // Apply filters
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['low_stock_only'])) {
            $query->whereColumn('current_stock', '<=', 'minimum_stock');
        }

        // Pagination
        $perPage = $filters['per_page'] ?? 50;
        $products = $query->orderBy('name')->paginate($perPage);

        // Calculate summary
        $allProducts = $query->get();
        $summary = [
            'total_products' => $allProducts->count(),
            'total_stock_value' => $allProducts->sum(function ($product) {
                return $product->current_stock * $product->cost_price;
            }),
            'low_stock_items' => $allProducts->filter(function ($product) {
                return $product->current_stock <= $product->minimum_stock;
            })->count(),
        ];

        return [
            'products' => $products,
            'summary' => $summary,
        ];
    }
}
