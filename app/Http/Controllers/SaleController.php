<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Sale::with(['cashier', 'customer', 'saleItems.product', 'promotion'])
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sale_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhereHas('cashier', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sales = $query->paginate(15)->withQueryString();

        // Calculate summary statistics
        $totalSales = Sale::sum('total_amount');
        $todaySales = Sale::whereDate('created_at', today())->sum('total_amount');
        $totalTransactions = Sale::count();
        $todayTransactions = Sale::whereDate('created_at', today())->count();

        return Inertia::render('Sales/Index', [
            'sales' => $sales,
            'filters' => $request->only(['search', 'date_from', 'date_to', 'payment_method', 'status']),
            'statistics' => [
                'total_sales' => $totalSales,
                'today_sales' => $todaySales,
                'total_transactions' => $totalTransactions,
                'today_transactions' => $todayTransactions,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('name')
            ->get();

        return Inertia::render('Sales/Create', [
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaleRequest $request)
    {
        try {
            DB::beginTransaction();

            // Generate sale number
            $saleNumber = 'SALE-' . date('Ymd') . '-' . str_pad(Sale::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            // Create sale record
            $sale = Sale::create([
                'sale_number' => $saleNumber,
                'cashier_id' => Auth::id(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'subtotal' => $request->subtotal,
                'tax_amount' => $request->tax_amount,
                'discount_amount' => $request->discount_amount ?? 0,
                'total_amount' => $request->total_amount,
                'payment_method' => $request->payment_method,
                'payment_received' => $request->payment_received,
                'change_amount' => $request->change_amount ?? 0,
                'notes' => $request->notes,
                'promotion_id' => $request->promotion_id,
                'status' => 'completed',
            ]);

            // Update promotion usage count if promotion is applied
            if ($request->promotion_id) {
                $promotion = \App\Models\Promotion::find($request->promotion_id);
                if ($promotion) {
                    $promotion->increment('used_count');
                }
            }

            // Create sale items and update product stock
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Check stock availability
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                // Create sale item
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);

                // Update product stock
                $product->decrement('stock_quantity', $item['quantity']);
            }

            DB::commit();

            return redirect()->route('sales.show', $sale->id)
                ->with('success', 'Sale completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        $sale->load(['cashier', 'customer', 'saleItems.product.category', 'promotion']);

        return Inertia::render('Sales/Show', [
            'sale' => $sale,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        // Only admin can edit completed sales
        if ($sale->status === 'completed' && Auth::user()->role !== 'admin') {
            return redirect()->route('sales.show', $sale->id)
                ->with('error', 'เฉพาะผู้ดูแลระบบเท่านั้นที่สามารถแก้ไขรายการขายที่เสร็จสมบูรณ์ได้');
        }

        $sale->load(['saleItems.product', 'customer']);
        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return Inertia::render('Sales/Edit', [
            'sale' => $sale,
            'products' => $products,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSaleRequest $request, Sale $sale)
    {
        // Only admin can update completed sales
        if ($sale->status === 'completed' && Auth::user()->role !== 'admin') {
            return back()->withErrors(['error' => 'เฉพาะผู้ดูแลระบบเท่านั้นที่สามารถแก้ไขรายการขายที่เสร็จสมบูรณ์ได้']);
        }

        try {
            DB::beginTransaction();

            // Restore original stock quantities
            foreach ($sale->saleItems as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('current_stock', $item->quantity);
                }
            }

            // If customer account payment, restore old outstanding balance
            if ($sale->payment_method === 'customer_account' && $sale->customer_id) {
                $customer = \App\Models\Customer::find($sale->customer_id);
                if ($customer) {
                    $customer->decrement('outstanding_balance', $sale->total_amount);
                }
            }

            // Delete existing sale items
            $sale->saleItems()->delete();

            // Handle promotion changes
            $oldPromotionId = $sale->promotion_id;
            $newPromotionId = $request->promotion_id;

            // If promotion changed, update usage counts
            if ($oldPromotionId !== $newPromotionId) {
                // Decrement old promotion usage count
                if ($oldPromotionId) {
                    $oldPromotion = \App\Models\Promotion::find($oldPromotionId);
                    if ($oldPromotion && $oldPromotion->used_count > 0) {
                        $oldPromotion->decrement('used_count');
                    }
                }

                // Increment new promotion usage count
                if ($newPromotionId) {
                    $newPromotion = \App\Models\Promotion::find($newPromotionId);
                    if ($newPromotion) {
                        $newPromotion->increment('used_count');
                    }
                }
            }

            // Update sale record
            $sale->update([
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'customer_id' => $request->customer_id,
                'subtotal' => $request->subtotal,
                'tax_amount' => $request->tax_amount,
                'discount_amount' => $request->discount_amount ?? 0,
                'total_amount' => $request->total_amount,
                'payment_method' => $request->payment_method,
                'payment_received' => $request->payment_received,
                'change_amount' => $request->change_amount ?? 0,
                'notes' => $request->notes,
                'promotion_id' => $request->promotion_id,
            ]);

            // Create new sale items and update product stock
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Check stock availability
                if ($product->current_stock < $item['quantity']) {
                    throw new \Exception("สต็อกไม่เพียงพอสำหรับสินค้า: {$product->name}");
                }

                // Create sale item
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);

                // Update product stock
                $product->decrement('current_stock', $item['quantity']);
            }

            // If customer account payment, add new outstanding balance
            if ($request->payment_method === 'customer_account' && $request->customer_id) {
                $customer = \App\Models\Customer::find($request->customer_id);
                if ($customer) {
                    // Check credit limit
                    $availableCredit = $customer->credit_limit - $customer->outstanding_balance;
                    if ($availableCredit < $request->total_amount) {
                        throw new \Exception("วงเงินเครดิตไม่เพียงพอ มีวงเงินคงเหลือ: ฿" . number_format($availableCredit, 2));
                    }
                    $customer->increment('outstanding_balance', $request->total_amount);
                }
            }

            DB::commit();

            return redirect()->route('sales.show', $sale->id)
                ->with('success', 'แก้ไขรายการขายสำเร็จ! สต็อกสินค้าได้รับการปรับปรุงแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        // Only admin can delete completed sales
        if ($sale->status === 'completed' && Auth::user()->role !== 'admin') {
            return back()->withErrors(['error' => 'Only administrators can delete completed sales.']);
        }

        try {
            DB::beginTransaction();

            // Restore stock quantities for all items
            foreach ($sale->saleItems as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    // Return stock back
                    $product->increment('current_stock', $item->quantity);
                }
            }

            // If customer account payment, restore outstanding balance
            if ($sale->payment_method === 'customer_account' && $sale->customer_id) {
                $customer = \App\Models\Customer::find($sale->customer_id);
                if ($customer) {
                    $customer->decrement('outstanding_balance', $sale->total_amount);
                }
            }

            // Delete sale items first
            $sale->saleItems()->delete();
            
            // Delete sale
            $sale->delete();

            DB::commit();

            return redirect()->route('sales.index')
                ->with('success', 'ลบรายการขายสำเร็จ! สต็อกสินค้าได้รับการคืนแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    /**
     * Search products for sale creation
     */
    public function searchProducts(Request $request)
    {
        $query = Product::with('category')
            ->where('is_active', true)
            ->where('current_stock', '>', 0);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $products = $query->orderBy('name')->limit(20)->get();

        return response()->json($products);
    }

    /**
     * Print receipt
     */
    public function printReceipt(Sale $sale)
    {
        $sale->load(['cashier', 'saleItems.product', 'customer']);

        $settings = \App\Models\ReceiptSettings::getSettings();

        return Inertia::render('Sales/Receipt', [
            'sale' => $sale,
            'settings' => $settings,
        ]);
    }
}
