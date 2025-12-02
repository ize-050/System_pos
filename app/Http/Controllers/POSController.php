<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Promotion;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class POSController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('role:admin,manager,cashier');
    }

    /**
     * Display the POS interface
     */
    public function index()
    {
        $categories = ProductCategory::where('is_active', true)
            ->withCount(['products' => function ($query) {
                $query
                    ->where('is_active', true)
                    ->where('current_stock', '>', 0);
            }])
            ->orderBy('name')
            ->get();

        $recentSales = Sale::with(['cashier', 'saleItems.product'])
            ->where('cashier_id', auth()->id())
            ->whereDate('created_at', today())
            ->latest()
            ->limit(5)
            ->get();

        $todayStats = $this->getTodayStats();

        return Inertia::render('POS', [
            'categories' => $categories,
            'recentSales' => $recentSales,
            'todayStats' => $todayStats,
            'cashier' => auth()->user()
        ]);
    }

    /**
     * Search products for POS
     */
    public function searchProducts(Request $request)
    {
        try {
            $query = Product::with('category')
                ->where('is_active', true);

            // Search by text (name, SKU, or barcode)
            $searchTerm = trim($request->input('query', $request->input('search', '')));
            if (!empty($searchTerm)) {
                $query->where(function ($q) use ($searchTerm) {
                    $q
                        ->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('sku', 'like', "%{$searchTerm}%")
                        ->orWhere('barcode', 'like', "%{$searchTerm}%");
                });
            }

            // Filter by category
            $categoryId = $request->input('category', $request->input('category_id', ''));
            if (!empty($categoryId) && $categoryId !== '' && $categoryId !== 'null' && $categoryId !== '0') {
                $query->where('category_id', $categoryId);
            }

            // Get products with ordering
            $products = $query
                ->orderBy('name', 'asc')
                ->limit(100)
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'barcode' => $product->barcode,
                        'selling_price' => (float) $product->selling_price,
                        'current_stock' => (int) $product->current_stock,
                        'category' => $product->category ? $product->category->name : 'ไม่มีหมวดหมู่',
                        'category_id' => $product->category_id,
                        'image' => $product->image_url
                    ];
                });

            return response()->json($products);
        } catch (\Exception $e) {
            \Log::error('Error searching products:', [
                'message' => $e->getMessage(),
                'query' => $request->input('query'),
                'category' => $request->input('category')
            ]);

            return response()->json([], 500);
        }
    }

    /**
     * Get product by barcode
     */
    public function getProductByBarcode($barcode)
    {
        $product = Product::with('category')
            ->where('barcode', $barcode)
            ->where('is_active', true)
            ->where('current_stock', '>', 0)
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'barcode' => $product->barcode,
            'selling_price' => $product->selling_price,
            'current_stock' => $product->current_stock,
            'category' => $product->category ? $product->category->name : null,
            'category_id' => $product->category_id,
            'image' => $product->image_url
        ]);
    }

    /**
     * Search customers for POS
     */
    public function searchCustomers(Request $request)
    {
        $query = Customer::where('is_active', true);

        if ($request->filled('query')) {
            $search = $request->get('query');
            $query->where(function ($q) use ($search) {
                $q
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('line_id', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('customer_code', 'like', "%{$search}%");
            });
        }

        $customers = $query
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'line_id' => $customer->line_id,
                    'company_name' => $customer->company_name,
                    'customer_code' => $customer->customer_code,
                    'customer_type' => $customer->customer_type,
                    'credit_limit' => $customer->credit_limit,
                    'outstanding_balance' => $customer->outstanding_balance,
                    'available_credit' => max(0, $customer->credit_limit - $customer->outstanding_balance)
                ];
            });

        return response()->json(['customers' => $customers]);
    }

    public function processSale(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer,credit_card,customer_account',
            'customer_id' => 'nullable|exists:customers,id',
            'received_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'promotion_id' => 'nullable|exists:promotions,id',
        ]);

        // Debug: Log customer_id
        \Log::info('Processing sale with customer_id: ' . $request->customer_id);

        try {
            DB::beginTransaction();

            // Calculate subtotal from items
            $subtotal = collect($request->items)->sum(function ($item) {
                return $item['quantity'] * $item['unit_price'];
            });

            $discountAmount = $request->discount_amount ?? 0;
            $taxAmount = $request->tax_amount ?? 0;
            $totalAmount = $request->total_amount;

            // Create sale record
            $sale = Sale::create([
                'sale_number' => $this->generateSaleNumber(),
                'customer_id' => $request->customer_id,
                'cashier_id' => auth()->id(),
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'received_amount' => $request->received_amount,
                'change_amount' => max(0, ($request->received_amount ?? 0) - $totalAmount),
                'status' => 'completed',
                'sale_date' => now(),
                'promotion_id' => $request->promotion_id,
            ]);

            // Create sale items and update stock
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Check stock availability
                if ($product->current_stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                // Create sale item
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);

                // Update product stock
                $product->decrement('current_stock', $item['quantity']);
            }

            // Update promotion usage if promotion was applied
            if ($request->promotion_id) {
                $promotion = Promotion::findOrFail($request->promotion_id);
                $promotion->increment('used_count');
            }

            // Update customer outstanding balance if credit sale
            if ($request->payment_method === 'customer_account' && $request->customer_id) {
                $customer = Customer::findOrFail($request->customer_id);

                // Check if customer has sufficient credit limit
                $availableCredit = $customer->credit_limit - $customer->outstanding_balance;
                if ($availableCredit < $totalAmount) {
                    throw new \Exception('Insufficient credit limit. Available: ฿' . number_format($availableCredit, 2) . ', Required: ฿' . number_format($totalAmount, 2));
                }

                $customer->increment('outstanding_balance', $totalAmount);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'sale_number' => $sale->sale_number,
                'sale' => $sale->load(['saleItems.product', 'customer', 'promotion']),
                'message' => 'Sale processed successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get daily summary for cashier
     */
    public function getDailySummary()
    {
        $today = today();
        $cashierId = auth()->id();

        $summary = Sale::where('cashier_id', $cashierId)
            ->whereDate('created_at', $today)
            ->selectRaw('
                COUNT(*) as total_sales,
                SUM(total_amount) as total_revenue,
                SUM(CASE WHEN payment_method = "cash" THEN total_amount ELSE 0 END) as cash_sales,
                SUM(CASE WHEN payment_method = "credit" THEN total_amount ELSE 0 END) as credit_sales,
                SUM(CASE WHEN payment_method = "customer_account" THEN total_amount ELSE 0 END) as account_sales
            ')
            ->first();

        return response()->json($summary);
    }

    /**
     * Get today's statistics
     */
    private function getTodayStats()
    {
        $today = today();
        $cashierId = auth()->id();

        return [
            'total_sales' => Sale::where('cashier_id', $cashierId)->whereDate('created_at', $today)->count(),
            'total_revenue' => Sale::where('cashier_id', $cashierId)->whereDate('created_at', $today)->sum('total_amount'),
            'cash_sales' => Sale::where('cashier_id', $cashierId)->whereDate('created_at', $today)->where('payment_method', 'cash')->sum('total_amount'),
            'credit_sales' => Sale::where('cashier_id', $cashierId)->whereDate('created_at', $today)->where('payment_method', 'credit')->sum('total_amount'),
        ];
    }

    /**
     * Generate unique sale number
     */
    private function generateSaleNumber()
    {
        $date = now()->format('Ymd');
        $lastSale = Sale::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastSale ? (int) substr($lastSale->sale_number, -4) + 1 : 1;

        return 'POS-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get active promotions for POS
     */
    public function getActivePromotions(Request $request)
    {
        try {
            $query = Promotion::where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where(function ($query) {
                    $query
                        ->whereNull('usage_limit')
                        ->orWhereColumn('usage_count', '<', 'usage_limit');
                });

            // If product IDs are provided, filter promotions
            if ($request->has('product_ids') && !empty($request->product_ids)) {
                $productIds = $request->product_ids;

                $query->where(function ($q) use ($productIds) {
                    // Include promotions that apply to all products (empty arrays)
                    $q->where(function ($subQ) {
                        $subQ
                            ->whereJsonLength('applicable_products', 0)
                            ->whereJsonLength('applicable_categories', 0);
                    });

                    // Or promotions that include specific products
                    foreach ($productIds as $productId) {
                        $q->orWhereJsonContains('applicable_products', (int) $productId);
                    }

                    // Or promotions that include categories of the products
                    // Get categories of the products in cart
                    $categoryIds = \App\Models\Product::whereIn('id', $productIds)
                        ->pluck('category_id')
                        ->unique()
                        ->toArray();

                    foreach ($categoryIds as $categoryId) {
                        $q->orWhereJsonContains('applicable_categories', (int) $categoryId);
                    }
                });
            }

            $promotions = $query->get();

            // Filter promotions based on cart total if provided
            if ($request->has('cart_total') && $request->cart_total > 0) {
                $cartTotal = $request->cart_total;
                $promotions = $promotions->filter(function ($promotion) use ($cartTotal) {
                    // If promotion has minimum amount requirement, check if cart total meets it
                    if ($promotion->min_amount && $cartTotal < $promotion->min_amount) {
                        return false;
                    }
                    return true;
                });
            }

            return response()->json($promotions->values());
        } catch (\Exception $e) {
            logger('Error fetching active promotions: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch promotions'], 500);
        }
    }

    /**
     * Apply promotion to cart items
     */
    public function applyPromotion(Request $request)
    {
        $request->validate([
            'promotion_id' => 'required|exists:promotions,id',
            'cart_items' => 'required|array',
            'cart_items.*.product_id' => 'required|exists:products,id',
            'cart_items.*.quantity' => 'required|integer|min:1',
            'cart_items.*.price' => 'required|numeric|min:0',
        ]);

        $promotion = Promotion::findOrFail($request->promotion_id);

        // Check if promotion is still valid
        if (!$promotion->is_active ||
                $promotion->start_date > now() ||
                $promotion->end_date < now() ||
                ($promotion->usage_limit && $promotion->used_count >= $promotion->usage_limit)) {
            return response()->json(['error' => 'โปรโมชั่นไม่สามารถใช้งานได้'], 400);
        }

        $cartItems = $request->cart_items;
        $discount = 0;
        $applicableItems = [];

        // Check which items are eligible for the promotion
        foreach ($cartItems as $item) {
            $product = Product::with('category')->find($item['product_id']);

            $isEligible = false;

            // Check if product is directly included in applicable_products JSON field
            if ($promotion->canApplyToProduct($product->id)) {
                $isEligible = true;
            }

            // Check if product's category is included in applicable_categories JSON field
            if (!$isEligible && $promotion->canApplyToCategory($product->category_id)) {
                $isEligible = true;
            }

            // If no specific products or categories, apply to all
            if (!$isEligible && empty($promotion->applicable_products) && empty($promotion->applicable_categories)) {
                $isEligible = true;
            }

            if ($isEligible) {
                $applicableItems[] = array_merge($item, ['product' => $product]);
            }
        }

        if (empty($applicableItems)) {
            return response()->json(['error' => 'ไม่มีสินค้าที่สามารถใช้โปรโมชั่นนี้ได้'], 400);
        }

        // Calculate subtotal of applicable items
        $subtotal = array_sum(array_map(function ($item) {
            return $item['price'] * $item['quantity'];
        }, $applicableItems));

        // Check minimum amount condition
        if ($promotion->min_amount && $subtotal < $promotion->min_amount) {
            return response()->json([
                'error' => 'ยอดซื้อขั้นต่ำไม่ถึง ฿' . number_format($promotion->min_amount, 2) . ' (ยอดปัจจุบัน ฿' . number_format($subtotal, 2) . ')'
            ], 400);
        }

        // Calculate discount based on promotion type
        switch ($promotion->type) {
            case 'percentage':
                $discount = $subtotal * ($promotion->value / 100);
                break;

            case 'fixed_amount':
                $discount = $promotion->value;
                break;

            case 'buy_x_get_y':
                $buyQuantity = $promotion->min_quantity;
                $getQuantity = $promotion->free_quantity;

                // Check if any item meets the minimum quantity requirement
                $hasValidQuantity = false;
                foreach ($applicableItems as $item) {
                    if ($item['quantity'] >= $buyQuantity) {
                        $hasValidQuantity = true;
                        $freeItems = floor($item['quantity'] / $buyQuantity) * $getQuantity;
                        $freeItems = min($freeItems, $item['quantity']);  // Can't get more free than total quantity
                        $discount += $freeItems * $item['price'];
                    }
                }

                // If no item meets the minimum quantity requirement
                if (!$hasValidQuantity) {
                    return response()->json([
                        'error' => 'จำนวนสินค้าไม่ถึงเงื่อนไข (ต้องซื้อขั้นต่ำ ' . $buyQuantity . ' ชิ้น)'
                    ], 400);
                }
                break;
        }

        // Apply maximum discount limit if set
        if ($promotion->max_discount && $discount > $promotion->max_discount) {
            $discount = $promotion->max_discount;
        }

        return response()->json([
            'discount' => $discount,
            'applicable_items' => $applicableItems,
            'promotion' => $promotion
        ]);
    }
}
