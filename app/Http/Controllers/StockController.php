<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Category;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'stocks' => function($query) {
            $query->latest();
        }]);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter by stock level
        if ($request->has('stock_filter') && $request->stock_filter) {
            $filter = $request->stock_filter;
            $query->whereHas('stocks', function($q) use ($filter) {
                switch ($filter) {
                    case 'out_of_stock':
                        $q->where('quantity', '<=', 0);
                        break;
                    case 'low_stock':
                        $q->where('quantity', '>', 0)->where('quantity', '<=', 10);
                        break;
                    case 'in_stock':
                        $q->where('quantity', '>', 10);
                        break;
                }
            });
        }

        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->orderBy('name')
                         ->paginate(20)
                         ->withQueryString();

        $categories = \App\Models\Category::orderBy('name')->get();

        return Inertia::render('Stocks/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['search', 'stock_filter', 'category_id'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::with('category')->orderBy('name')->get();
        
        return Inertia::render('Stocks/Create', [
            'products' => $products
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:in,out,adjustment',
            'reason' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:100'
        ]);

        $product = Product::findOrFail($request->product_id);
        $currentStock = $product->stocks()->latest()->first();
        $currentQuantity = $currentStock ? $currentStock->quantity : 0;

        // Calculate new quantity based on type
        $newQuantity = $currentQuantity;
        switch ($request->type) {
            case 'in':
                $newQuantity += $request->quantity;
                break;
            case 'out':
                $newQuantity -= $request->quantity;
                break;
            case 'adjustment':
                $newQuantity = $request->quantity;
                break;
        }

        // Ensure quantity doesn't go below 0
        $newQuantity = max(0, $newQuantity);

        // Create stock record
        Stock::create([
            'product_id' => $request->product_id,
            'quantity' => $newQuantity,
            'type' => $request->type,
            'change_quantity' => $request->type === 'adjustment' 
                ? ($newQuantity - $currentQuantity) 
                : ($request->type === 'in' ? $request->quantity : -$request->quantity),
            'reason' => $request->reason,
            'reference' => $request->reference,
            'created_by' => auth()->id()
        ]);

        return redirect()->route('stocks.index')
                        ->with('success', 'บันทึกการเคลื่อนไหวสต็อกเรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with(['category', 'stocks' => function($query) {
            $query->with('creator')->orderBy('created_at', 'desc');
        }])->findOrFail($id);

        return Inertia::render('Stocks/Show', [
            'product' => $product
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $stock = Stock::with(['product', 'creator'])->findOrFail($id);
        
        return Inertia::render('Stocks/Edit', [
            'stock' => $stock
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:100'
        ]);

        $stock = Stock::findOrFail($id);
        
        $stock->update([
            'reason' => $request->reason,
            'reference' => $request->reference,
            'updated_by' => auth()->id()
        ]);

        return redirect()->route('stocks.index')
                        ->with('success', 'อัพเดทข้อมูลสต็อกเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $stock = Stock::findOrFail($id);
        
        // Only allow deletion of the latest stock record
        $latestStock = Stock::where('product_id', $stock->product_id)
                           ->latest()
                           ->first();
        
        if ($stock->id !== $latestStock->id) {
            return redirect()->back()
                           ->with('error', 'สามารถลบได้เฉพาะรายการสต็อกล่าสุดเท่านั้น');
        }

        $stock->delete();

        return redirect()->route('stocks.index')
                        ->with('success', 'ลบรายการสต็อกเรียบร้อยแล้ว');
    }

    /**
     * Get low stock products for alerts
     */
    public function getLowStockProducts()
    {
        $products = Product::with(['category', 'stocks' => function($query) {
            $query->latest();
        }])
        ->whereHas('stocks', function($query) {
            $query->where('quantity', '<=', 10);
        })
        ->orderBy('name')
        ->get();

        return response()->json($products);
    }

    /**
     * Bulk stock adjustment
     */
    public function bulkAdjustment(Request $request)
    {
        $request->validate([
            'adjustments' => 'required|array',
            'adjustments.*.product_id' => 'required|exists:products,id',
            'adjustments.*.quantity' => 'required|integer|min:0',
            'reason' => 'nullable|string|max:255'
        ]);

        foreach ($request->adjustments as $adjustment) {
            $product = Product::findOrFail($adjustment['product_id']);
            $currentStock = $product->stocks()->latest()->first();
            $currentQuantity = $currentStock ? $currentStock->quantity : 0;
            
            Stock::create([
                'product_id' => $adjustment['product_id'],
                'quantity' => $adjustment['quantity'],
                'type' => 'adjustment',
                'change_quantity' => $adjustment['quantity'] - $currentQuantity,
                'reason' => $request->reason ?? 'Bulk adjustment',
                'reference' => 'BULK-' . now()->format('YmdHis'),
                'created_by' => auth()->id()
            ]);
        }

        return redirect()->route('stocks.index')
                        ->with('success', 'ปรับปรุงสต็อกจำนวน ' . count($request->adjustments) . ' รายการเรียบร้อยแล้ว');
    }
}
