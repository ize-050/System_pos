<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of purchase orders.
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'createdBy'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by supplier
        if ($request->has('supplier_id') && $request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('po_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('supplier', function ($sq) use ($request) {
                      $sq->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $purchaseOrders = $query->paginate(15);

        // Statistics
        $statistics = [
            'draft' => PurchaseOrder::where('status', 'draft')->count(),
            'pending' => PurchaseOrder::where('status', 'pending')->count(),
            'shipping' => PurchaseOrder::where('status', 'shipping')->count(),
            'received' => PurchaseOrder::where('status', 'received')->count(),
            'cancelled' => PurchaseOrder::where('status', 'cancelled')->count(),
        ];

        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);

        return Inertia::render('PurchaseOrders/Index', [
            'purchaseOrders' => $purchaseOrders->toArray(),
            'statistics' => $statistics,
            'suppliers' => $suppliers->toArray(),
            'filters' => $request->only(['status', 'supplier_id', 'search']) ?: ['status' => 'all', 'supplier_id' => '', 'search' => ''],
        ]);
    }

    /**
     * Show the form for creating a new purchase order.
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = \App\Models\Product::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'cost_price', 'current_stock']);

        return Inertia::render('PurchaseOrders/Create', [
            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created purchase order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_name' => 'required|string',
            'items.*.quantity_ordered' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'include_vat' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        // Generate PO number
        $poNumber = PurchaseOrder::generatePONumber();

        // Calculate totals
        $subtotal = collect($validated['items'])->sum(function ($item) {
            return $item['quantity_ordered'] * $item['unit_price'];
        });

        $discountAmount = $validated['discount_amount'] ?? 0;
        $afterDiscount = $subtotal - $discountAmount;
        $includeVat = $validated['include_vat'] ?? true;
        $vatAmount = $includeVat ? ($afterDiscount * 0.07) : 0; // 7% VAT if included
        $totalAmount = $afterDiscount + $vatAmount;

        // Create PO
        $po = PurchaseOrder::create([
            'po_number' => $poNumber,
            'supplier_id' => $validated['supplier_id'],
            'order_date' => $validated['order_date'],
            'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'vat_amount' => $vatAmount,
            'total_amount' => $totalAmount,
            'status' => 'draft',
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        // Create PO items
        foreach ($validated['items'] as $item) {
            $po->items()->create([
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'quantity_ordered' => $item['quantity_ordered'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity_ordered'] * $item['unit_price'],
            ]);
        }

        return redirect()->route('purchase-orders.show', $po->id)
            ->with('success', 'Purchase Order created successfully');
    }

    /**
     * Display the specified purchase order.
     */
    public function show($id)
    {
        $po = PurchaseOrder::with([
            'supplier',
            'items.product',
            'createdBy',
            'updatedBy',
            'receivedBy'
        ])->findOrFail($id);

        $settings = \App\Models\ReceiptSettings::getSettings();

        return Inertia::render('PurchaseOrders/Show', [
            'purchaseOrder' => $po,
            'settings' => $settings,
        ]);
    }

    /**
     * Show the form for editing the specified purchase order.
     */
    public function edit($id)
    {
        $po = PurchaseOrder::with(['supplier', 'items.product'])->findOrFail($id);

        // Only allow edit if status is draft
        if ($po->status !== 'draft') {
            return back()->with('error', 'ไม่สามารถแก้ไขใบสั่งซื้อที่ไม่ใช่สถานะฉบับร่างได้');
        }

        $suppliers = Supplier::orderBy('name')->get();
        $products = \App\Models\Product::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'cost_price']);

        return Inertia::render('PurchaseOrders/Edit', [
            'purchaseOrder' => $po,
            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    /**
     * Update the specified purchase order.
     */
    public function update(Request $request, $id)
    {
        $po = PurchaseOrder::findOrFail($id);

        // Only allow edit if status is draft
        if ($po->status !== 'draft') {
            return back()->with('error', 'Cannot edit purchase order that is not in draft status');
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_name' => 'required|string',
            'items.*.quantity_ordered' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'include_vat' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        // Calculate totals
        $subtotal = collect($validated['items'])->sum(function ($item) {
            return $item['quantity_ordered'] * $item['unit_price'];
        });

        $discountAmount = $validated['discount_amount'] ?? 0;
        $afterDiscount = $subtotal - $discountAmount;
        $includeVat = $validated['include_vat'] ?? true;
        $vatAmount = $includeVat ? ($afterDiscount * 0.07) : 0;
        $totalAmount = $afterDiscount + $vatAmount;

        // Update PO
        $po->update([
            'supplier_id' => $validated['supplier_id'],
            'order_date' => $validated['order_date'],
            'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'vat_amount' => $vatAmount,
            'total_amount' => $totalAmount,
            'notes' => $validated['notes'] ?? null,
            'updated_by' => auth()->id(),
        ]);

        // Delete old items and create new ones
        $po->items()->delete();
        foreach ($validated['items'] as $item) {
            $po->items()->create([
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'quantity_ordered' => $item['quantity_ordered'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity_ordered'] * $item['unit_price'],
            ]);
        }

        return redirect()->route('purchase-orders.show', $po->id)
            ->with('success', 'Purchase Order updated successfully');
    }

    /**
     * Remove the specified purchase order (cancel).
     */
    public function destroy($id)
    {
        $po = PurchaseOrder::findOrFail($id);

        // Can only cancel if not received
        if ($po->status === 'received') {
            return back()->with('error', 'Cannot cancel received purchase order');
        }

        $po->update([
            'status' => 'cancelled',
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order cancelled successfully');
    }

    /**
     * Send purchase order (change status from draft to pending).
     */
    public function sendOrder($id)
    {
        $po = PurchaseOrder::findOrFail($id);

        if (!$po->canTransitionTo('pending')) {
            return back()->with('error', 'Cannot send this purchase order');
        }

        $po->update([
            'status' => 'pending',
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Purchase Order sent successfully');
    }

    /**
     * Show receive goods form.
     */
    public function receiveGoods($id)
    {
        $po = PurchaseOrder::with(['items.product', 'supplier'])->findOrFail($id);

        if (!in_array($po->status, ['pending', 'shipping'])) {
            return back()->with('error', 'Cannot receive goods for this purchase order');
        }

        return Inertia::render('PurchaseOrders/ReceiveGoods', [
            'purchaseOrder' => $po,
        ]);
    }

    /**
     * Store received goods and update stock.
     */
    public function storeReceivedGoods(Request $request, $id)
    {
        $po = PurchaseOrder::findOrFail($id);

        if (!in_array($po->status, ['pending', 'shipping'])) {
            return back()->with('error', 'Cannot receive goods for this purchase order');
        }

        $validated = $request->validate([
            'received_date' => 'required|date',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity_received' => 'required|numeric|min:0',
            'items.*.condition_status' => 'required|in:good,partial_damaged,fully_damaged',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($po, $validated) {
            // Prepare bulk updates
            $itemUpdates = [];
            $stockUpdates = [];
            
            foreach ($validated['items'] as $itemData) {
                $item = $po->items()->findOrFail($itemData['id']);
                
                // Update item
                $item->update([
                    'quantity_received' => $itemData['quantity_received'],
                    'condition_status' => $itemData['condition_status'],
                    'notes' => $itemData['notes'] ?? null,
                ]);

                // Collect stock updates
                if ($item->product_id && $itemData['quantity_received'] > 0) {
                    $stockUpdates[$item->product_id] = ($stockUpdates[$item->product_id] ?? 0) + $itemData['quantity_received'];
                }
            }

            // Bulk update product stocks
            foreach ($stockUpdates as $productId => $quantity) {
                \App\Models\Product::where('id', $productId)->increment('current_stock', $quantity);
            }

            // Update PO status
            $po->update([
                'status' => 'received',
                'received_date' => $validated['received_date'],
                'received_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        });

        return redirect()->route('purchase-orders.show', $po->id)
            ->with('success', 'Goods received and stock updated successfully');
    }
}
