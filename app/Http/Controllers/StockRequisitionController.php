<?php

namespace App\Http\Controllers;

use App\Models\StockRequisition;
use App\Models\StockRequisitionItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class StockRequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StockRequisition::with(['createdBy', 'items'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('requisition_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('requisition_date', '<=', $request->end_date);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('requisition_number', 'like', "%{$search}%")
                  ->orWhere('requester_name', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('project_name', 'like', "%{$search}%");
            });
        }

        $requisitions = $query->paginate(15)->withQueryString();

        return Inertia::render('StockRequisitions/Index', [
            'requisitions' => $requisitions,
            'filters' => $request->only(['status', 'start_date', 'end_date', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::where('is_active', true)
            ->where('current_stock', '>', 0)
            ->orderBy('name')
            ->get(['id', 'sku', 'name', 'cost_price', 'current_stock', 'unit']);

        return Inertia::render('StockRequisitions/Create', [
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'requester_name' => 'required|string|max:200',
            'requisition_date' => 'required|date',
            'department' => 'nullable|string|max:200',
            'project_name' => 'nullable|string|max:200',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Create requisition
            $requisition = StockRequisition::create([
                'requisition_number' => StockRequisition::generateRequisitionNumber(),
                'requisition_date' => $request->requisition_date,
                'requester_name' => $request->requester_name,
                'department' => $request->department,
                'project_name' => $request->project_name,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Create items
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                StockRequisitionItem::create([
                    'requisition_id' => $requisition->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'cost_price' => $product->cost_price,
                    'total_cost' => $item['quantity'] * $product->cost_price,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            // Calculate totals
            $requisition->calculateTotals();

            DB::commit();

            return redirect()->route('stock-requisitions.show', $requisition)
                ->with('success', 'สร้างใบเบิกสินค้าสำเร็จ');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StockRequisition $stockRequisition)
    {
        $stockRequisition->load(['items.product', 'createdBy']);

        return Inertia::render('StockRequisitions/Show', [
            'requisition' => $stockRequisition,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockRequisition $stockRequisition)
    {
        if ($stockRequisition->status !== 'draft') {
            return redirect()->route('stock-requisitions.show', $stockRequisition)
                ->with('error', 'ไม่สามารถแก้ไขใบเบิกที่ดำเนินการแล้ว');
        }

        $stockRequisition->load(['items.product']);

        $products = Product::where('is_active', true)
            ->where('current_stock', '>', 0)
            ->orderBy('name')
            ->get(['id', 'sku', 'name', 'cost_price', 'current_stock', 'unit']);

        return Inertia::render('StockRequisitions/Edit', [
            'requisition' => $stockRequisition,
            'products' => $products,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockRequisition $stockRequisition)
    {
        if ($stockRequisition->status !== 'draft') {
            return back()->with('error', 'ไม่สามารถแก้ไขใบเบิกที่ดำเนินการแล้ว');
        }

        $request->validate([
            'requester_name' => 'required|string|max:200',
            'requisition_date' => 'required|date',
            'department' => 'nullable|string|max:200',
            'project_name' => 'nullable|string|max:200',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Update requisition
            $stockRequisition->update([
                'requisition_date' => $request->requisition_date,
                'requester_name' => $request->requester_name,
                'department' => $request->department,
                'project_name' => $request->project_name,
                'reason' => $request->reason,
                'notes' => $request->notes,
            ]);

            // Delete old items
            $stockRequisition->items()->delete();

            // Create new items
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                StockRequisitionItem::create([
                    'requisition_id' => $stockRequisition->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'cost_price' => $product->cost_price,
                    'total_cost' => $item['quantity'] * $product->cost_price,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            // Calculate totals
            $stockRequisition->calculateTotals();

            DB::commit();

            return redirect()->route('stock-requisitions.show', $stockRequisition)
                ->with('success', 'แก้ไขใบเบิกสินค้าสำเร็จ');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockRequisition $stockRequisition)
    {
        if ($stockRequisition->status !== 'draft') {
            return back()->with('error', 'ไม่สามารถลบใบเบิกที่ดำเนินการแล้ว');
        }

        $stockRequisition->delete();

        return redirect()->route('stock-requisitions.index')
            ->with('success', 'ลบใบเบิกสินค้าสำเร็จ');
    }

    /**
     * Complete the requisition (deduct stock)
     */
    public function complete(StockRequisition $stockRequisition)
    {
        if ($stockRequisition->status !== 'draft') {
            return back()->with('error', 'ไม่สามารถดำเนินการใบเบิกนี้ได้');
        }

        // Check stock availability
        foreach ($stockRequisition->items as $item) {
            if ($item->product->current_stock < $item->quantity) {
                return back()->with('error', "สินค้า {$item->product->name} มีไม่เพียงพอ (คงเหลือ: {$item->product->current_stock})");
            }
        }

        $stockRequisition->complete();

        return back()->with('success', 'ดำเนินการเบิกสินค้าสำเร็จ');
    }

    /**
     * Cancel the requisition
     */
    public function cancel(Request $request, StockRequisition $stockRequisition)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $stockRequisition->cancel($request->reason);

        return back()->with('success', 'ยกเลิกใบเบิกสินค้าสำเร็จ');
    }

    /**
     * Print requisition
     */
    public function print(StockRequisition $stockRequisition)
    {
        $stockRequisition->load(['items.product', 'createdBy']);

        return Inertia::render('StockRequisitions/Print', [
            'requisition' => $stockRequisition,
        ]);
    }

    /**
     * Search products for requisition
     */
    public function searchProducts(Request $request)
    {
        $search = $request->get('search', '');
        
        $products = Product::where('is_active', true)
            ->where('current_stock', '>', 0)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'sku', 'name', 'cost_price', 'current_stock', 'unit']);

        return response()->json($products);
    }
}
