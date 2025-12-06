<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Refund;
use App\Models\RefundItem;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RefundController extends Controller
{
    /**
     * Display a listing of refunds.
     */
    public function index(Request $request)
    {
        $query = Refund::with(['sale', 'customer', 'processedBy', 'items'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('refund_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('refund_date', '<=', $request->end_date);
        }

        // Search by refund number or sale number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('refund_number', 'like', "%{$search}%")
                  ->orWhereHas('sale', function ($q2) use ($search) {
                      $q2->where('sale_number', 'like', "%{$search}%");
                  });
            });
        }

        $refunds = $query->paginate(15)->withQueryString();

        // Summary statistics
        $summary = [
            'total_refunds' => Refund::count(),
            'pending_refunds' => Refund::where('status', 'pending')->count(),
            'completed_refunds' => Refund::where('status', 'completed')->count(),
            'total_refund_amount' => Refund::where('status', 'completed')->sum('total_amount'),
        ];

        return Inertia::render('Refunds/Index', [
            'refunds' => $refunds,
            'summary' => $summary,
            'filters' => $request->only(['status', 'start_date', 'end_date', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new refund.
     */
    public function create(Request $request)
    {
        $sale = null;
        $saleItems = [];

        if ($request->filled('sale_id')) {
            $sale = Sale::with(['customer', 'saleItems.product'])->find($request->sale_id);
            if ($sale) {
                // Get items that can be refunded (not already fully refunded)
                $saleItems = $sale->saleItems->map(function ($item) {
                    $refundedQty = RefundItem::whereHas('refund', function ($q) use ($item) {
                        $q->where('sale_id', $item->sale_id)
                          ->whereIn('status', ['approved', 'completed']);
                    })->where('sale_item_id', $item->id)->sum('quantity');

                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name ?? $item->product_name,
                        'quantity_sold' => $item->quantity,
                        'quantity_refunded' => $refundedQty,
                        'quantity_available' => $item->quantity - $refundedQty,
                        'unit_price' => $item->unit_price,
                    ];
                })->filter(fn($item) => $item['quantity_available'] > 0)->values();
            }
        }

        return Inertia::render('Refunds/Create', [
            'sale' => $sale,
            'saleItems' => $saleItems,
        ]);
    }

    /**
     * Search for a sale by sale number.
     */
    public function searchSale(Request $request)
    {
        $search = $request->get('q', '');
        
        $sales = Sale::with(['customer', 'saleItems.product'])
            ->where('status', 'completed')
            ->where(function ($q) use ($search) {
                $q->where('sale_number', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'sale_number' => $sale->sale_number,
                    'sale_date' => $sale->sale_date,
                    'total_amount' => $sale->total_amount,
                    'customer_name' => $sale->customer->name ?? 'ลูกค้าทั่วไป',
                    'items_count' => $sale->saleItems->count(),
                ];
            });

        return response()->json($sales);
    }

    /**
     * Store a newly created refund.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'items' => 'required|array|min:1',
            'items.*.sale_item_id' => 'required|exists:sale_items,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.return_to_stock' => 'boolean',
            'items.*.condition' => 'nullable|in:good,damaged,defective',
            'refund_method' => 'required|in:cash,transfer,credit_card,store_credit',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $sale = Sale::with('saleItems')->findOrFail($request->sale_id);

            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $saleItem = $sale->saleItems->find($item['sale_item_id']);
                if ($saleItem) {
                    $subtotal += $saleItem->unit_price * $item['quantity'];
                }
            }

            // Calculate tax (if original sale had tax)
            $taxRate = $sale->tax_amount > 0 ? ($sale->tax_amount / $sale->subtotal) : 0;
            $taxAmount = $subtotal * $taxRate;
            $totalAmount = $subtotal + $taxAmount;

            // Create refund
            $refund = Refund::create([
                'refund_number' => Refund::generateRefundNumber(),
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'processed_by' => auth()->id(),
                'refund_date' => now(),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'refund_method' => $request->refund_method,
                'status' => 'pending',
                'reason' => $request->reason,
                'notes' => $request->notes,
            ]);

            // Create refund items
            foreach ($request->items as $item) {
                $saleItem = $sale->saleItems->find($item['sale_item_id']);
                if ($saleItem) {
                    RefundItem::create([
                        'refund_id' => $refund->id,
                        'product_id' => $item['product_id'],
                        'sale_item_id' => $item['sale_item_id'],
                        'product_name' => $saleItem->product->name ?? $saleItem->product_name,
                        'quantity' => $item['quantity'],
                        'unit_price' => $saleItem->unit_price,
                        'total_price' => $saleItem->unit_price * $item['quantity'],
                        'return_to_stock' => $item['return_to_stock'] ?? true,
                        'condition' => $item['condition'] ?? 'good',
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('refunds.show', $refund->id)
                ->with('success', 'สร้างใบคืนสินค้าเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified refund.
     */
    public function show(Refund $refund)
    {
        $refund->load(['sale', 'customer', 'processedBy', 'items.product']);
        
        // Add processed_by_user for frontend
        $refundData = $refund->toArray();
        $refundData['processed_by_user'] = $refund->processedBy;

        return Inertia::render('Refunds/Show', [
            'refund' => $refundData,
        ]);
    }

    /**
     * Approve the refund.
     */
    public function approve(Refund $refund)
    {
        if ($refund->status !== 'pending') {
            return back()->withErrors(['error' => 'ไม่สามารถอนุมัติใบคืนสินค้านี้ได้']);
        }

        $refund->update(['status' => 'approved']);

        return back()->with('success', 'อนุมัติใบคืนสินค้าเรียบร้อยแล้ว');
    }

    /**
     * Complete the refund (process return to stock and update sale status).
     */
    public function complete(Refund $refund)
    {
        if (!in_array($refund->status, ['pending', 'approved'])) {
            return back()->withErrors(['error' => 'ไม่สามารถดำเนินการใบคืนสินค้านี้ได้']);
        }

        try {
            DB::beginTransaction();

            $sale = $refund->sale;

            // 1. Return items to stock (if condition is good)
            foreach ($refund->items as $item) {
                if ($item->return_to_stock && $item->condition === 'good') {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('current_stock', $item->quantity);
                    }
                }
            }

            // 2. Update refund status
            $refund->update(['status' => 'completed']);

            // 3. Calculate total refunded for this sale
            $totalRefundedAmount = Refund::where('sale_id', $sale->id)
                ->where('status', 'completed')
                ->sum('total_amount');

            // 4. Update sale's refunded amount
            $sale->update([
                'refunded_amount' => $totalRefundedAmount,
            ]);

            // 5. Check if all items in sale are refunded → mark as "refunded"
            $totalSoldQty = $sale->saleItems->sum('quantity');
            $totalRefundedQty = RefundItem::whereHas('refund', function ($q) use ($sale) {
                $q->where('sale_id', $sale->id)->where('status', 'completed');
            })->sum('quantity');

            if ($totalRefundedQty >= $totalSoldQty) {
                $sale->update(['status' => 'refunded']);
            } elseif ($totalRefundedAmount > 0) {
                // Partial refund - mark as partial_refunded if not fully refunded
                $sale->update(['status' => 'partial_refunded']);
            }

            // 6. If customer has account and refund method is store_credit, update balance
            if ($sale->customer_id && $refund->refund_method === 'store_credit') {
                $customer = Customer::find($sale->customer_id);
                if ($customer) {
                    $customer->decrement('current_balance', $refund->total_amount);
                }
            }

            DB::commit();

            return back()->with('success', 'ดำเนินการคืนสินค้าเรียบร้อยแล้ว - สต็อกและยอดขายถูกปรับปรุงแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject the refund.
     */
    public function reject(Request $request, Refund $refund)
    {
        if ($refund->status !== 'pending') {
            return back()->withErrors(['error' => 'ไม่สามารถปฏิเสธใบคืนสินค้านี้ได้']);
        }

        $refund->update([
            'status' => 'rejected',
            'notes' => $refund->notes . "\n[ปฏิเสธ] " . ($request->reject_reason ?? ''),
        ]);

        return back()->with('success', 'ปฏิเสธใบคืนสินค้าเรียบร้อยแล้ว');
    }

    /**
     * Print refund receipt.
     */
    public function print(Refund $refund)
    {
        $refund->load(['sale', 'customer', 'processedBy', 'items.product']);
        
        // Add processed_by_user for frontend
        $refundData = $refund->toArray();
        $refundData['processed_by_user'] = $refund->processedBy;

        return Inertia::render('Refunds/Print', [
            'refund' => $refundData,
        ]);
    }
}
