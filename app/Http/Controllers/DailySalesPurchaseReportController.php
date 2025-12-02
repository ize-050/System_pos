<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class DailySalesPurchaseReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('role:admin,manager,accountant');
    }

    /**
     * Display the daily sales-purchase report page
     */
    public function index(Request $request)
    {
        $categories = ProductCategory::orderBy('name')->get(['id', 'name']);
        $initialDate = $request->get('date', now()->toDateString());

        return Inertia::render('Reports/DailySalesPurchase', [
            'categories' => $categories,
            'initialDate' => $initialDate,
        ]);
    }

    /**
     * Get daily sales-purchase data via API
     */
    public function getData(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $categoryId = $request->get('category_id');

        // Get sales data for the day
        $salesData = $this->getSalesData($date, $categoryId);
        
        // Get purchase data for the day
        $purchaseData = $this->getPurchaseData($date, $categoryId);
        
        // Merge sales and purchase data
        $mergedData = $this->mergeSalesPurchaseData($salesData, $purchaseData);
        
        // Get inventory data
        $inventoryData = $this->getInventoryData($categoryId);

        return response()->json([
            'sales_data' => $mergedData,
            'inventory_data' => $inventoryData,
        ]);
    }

    /**
     * Get sales data for a specific date
     */
    private function getSalesData($date, $categoryId = null)
    {
        $query = SaleItem::select(
            'products.id as product_id',
            'products.sku',
            'products.name as product_name',
            'products.current_stock',
            'products.cost_price',
            'products.selling_price',
            'product_categories.name as category_name',
            DB::raw('SUM(sale_items.quantity) as sold_quantity'),
            DB::raw('SUM(sale_items.total_price) as total_sales_amount')
        )
        ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
        ->join('products', 'sale_items.product_id', '=', 'products.id')
        ->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
        ->whereDate('sales.created_at', $date)
        ->where('sales.status', 'completed')
        ->groupBy('products.id', 'products.sku', 'products.name', 'products.current_stock', 'products.cost_price', 'products.selling_price', 'product_categories.name');

        if ($categoryId) {
            $query->where('products.category_id', $categoryId);
        }

        return $query->get();
    }

    /**
     * Get purchase data for a specific date
     */
    private function getPurchaseData($date, $categoryId = null)
    {
        $query = PurchaseOrderItem::select(
            'products.id as product_id',
            'products.sku',
            'products.name as product_name',
            DB::raw('SUM(purchase_order_items.quantity_received) as purchased_quantity'),
            DB::raw('SUM(purchase_order_items.total_price) as total_purchase_amount')
        )
        ->join('purchase_orders', 'purchase_order_items.po_id', '=', 'purchase_orders.id')
        ->join('products', 'purchase_order_items.product_id', '=', 'products.id')
        ->where(function($q) use ($date) {
            $q->whereDate('purchase_orders.received_date', $date)
              ->orWhereDate('purchase_orders.created_at', $date);
        })
        ->whereIn('purchase_orders.status', ['received', 'completed'])
        ->groupBy('products.id', 'products.sku', 'products.name');

        if ($categoryId) {
            $query->where('products.category_id', $categoryId);
        }

        return $query->get();
    }

    /**
     * Merge sales and purchase data
     */
    private function mergeSalesPurchaseData($salesData, $purchaseData)
    {
        $merged = [];
        $productIds = collect($salesData)->pluck('product_id')
            ->merge(collect($purchaseData)->pluck('product_id'))
            ->unique();

        foreach ($productIds as $productId) {
            $sale = $salesData->firstWhere('product_id', $productId);
            $purchase = $purchaseData->firstWhere('product_id', $productId);

            if ($sale || $purchase) {
                // Get current stock from database
                $product = Product::find($productId);
                
                $merged[] = [
                    'product_id' => $productId,
                    'sku' => $sale->sku ?? $purchase->sku ?? $product->sku ?? '-',
                    'product_name' => $sale->product_name ?? $purchase->product_name ?? $product->name ?? '-',
                    'category_name' => $sale->category_name ?? $product->category->name ?? '-',
                    'sold_quantity' => (int)($sale->sold_quantity ?? 0),
                    'purchased_quantity' => (int)($purchase->purchased_quantity ?? 0),
                    'current_stock' => $product ? $product->current_stock : 0,
                    'cost_price' => $product ? $product->cost_price : 0,
                    'selling_price' => $product ? $product->selling_price : 0,
                    'total_sales_amount' => $sale->total_sales_amount ?? 0,
                    'total_purchase_amount' => $purchase->total_purchase_amount ?? 0,
                    'notes' => null,
                ];
            }
        }

        return collect($merged)->sortBy('product_name')->values()->all();
    }

    /**
     * Get current inventory data
     */
    private function getInventoryData($categoryId = null)
    {
        $query = Product::select(
            'products.id as product_id',
            'products.sku',
            'products.name as product_name',
            'products.current_stock',
            'products.cost_price',
            'products.reorder_point',
            'product_categories.name as category_name',
            DB::raw('(products.current_stock * products.cost_price) as inventory_value')
        )
        ->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
        ->where('products.is_active', true);

        if ($categoryId) {
            $query->where('products.category_id', $categoryId);
        }

        return $query->orderBy('products.name')->get();
    }
}
