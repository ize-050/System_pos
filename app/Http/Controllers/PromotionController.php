<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Promotion::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== null) {
            $query->where('is_active', $request->status);
        }

        $promotions = $query->orderBy('created_at', 'desc')
                           ->paginate(10)
                           ->withQueryString();

        // Map database field names to frontend field names for each promotion
        $promotions->getCollection()->transform(function ($promotion) {
            $promotionData = $promotion->toArray();
            $promotionData['minimum_quantity'] = $promotion->min_quantity;
            $promotionData['minimum_amount'] = $promotion->min_amount;
            $promotionData['maximum_discount'] = $promotion->max_discount;
            return $promotionData;
        });

        return Inertia::render('Promotions/Index', [
            'promotions' => $promotions,
            'filters' => $request->only(['search', 'type', 'status'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $products = Product::select('id', 'name')->get();
        $categories = ProductCategory::select('id', 'name')->get();

        return Inertia::render('Promotions/Create', [
            'products' => $products,
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed_amount,buy_x_get_y',
            'value' => 'required|numeric|min:0',
            'minimum_quantity' => 'nullable|integer|min:1',
            'free_quantity' => 'nullable|integer|min:1',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'applicable_products' => 'nullable|array',
            'applicable_categories' => 'nullable|array',
            'usage_limit' => 'nullable|integer|min:1'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Map frontend field names to database field names
        $data = $request->all();
        if (isset($data['minimum_quantity'])) {
            $data['min_quantity'] = $data['minimum_quantity'];
            unset($data['minimum_quantity']);
        }
        if (isset($data['minimum_amount'])) {
            $data['min_amount'] = $data['minimum_amount'];
            unset($data['minimum_amount']);
        }
        if (isset($data['maximum_discount'])) {
            $data['max_discount'] = $data['maximum_discount'];
            unset($data['maximum_discount']);
        }

        $promotion = Promotion::create($data);

        return redirect()->route('promotions.index')
                        ->with('success', 'โปรโมชั่นถูกสร้างเรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Promotion $promotion)
    {
        // Map database field names to frontend field names for display
        $promotionData = $promotion->toArray();
        $promotionData['minimum_quantity'] = $promotion->min_quantity;
        $promotionData['minimum_amount'] = $promotion->min_amount;
        $promotionData['maximum_discount'] = $promotion->max_discount;

        $products = Product::select('id', 'name')->get();
        $categories = ProductCategory::select('id', 'name')->get();

        return Inertia::render('Promotions/Show', [
            'promotion' => $promotionData,
            'products' => $products,
            'categories' => $categories
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Promotion $promotion)
    {
        $products = Product::select('id', 'name')->get();
        $categories = ProductCategory::select('id', 'name')->get();

        // Map database field names to frontend field names for editing
        $promotionData = $promotion->toArray();
        $promotionData['minimum_quantity'] = $promotion->min_quantity;
        $promotionData['minimum_amount'] = $promotion->min_amount;
        $promotionData['maximum_discount'] = $promotion->max_discount;

        return Inertia::render('Promotions/Edit', [
            'promotion' => $promotionData,
            'products' => $products,
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Promotion $promotion)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed_amount,buy_x_get_y',
            'value' => 'required|numeric|min:0',
            'minimum_quantity' => 'nullable|integer|min:1',
            'free_quantity' => 'nullable|integer|min:1',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'applicable_products' => 'nullable|array',
            'applicable_categories' => 'nullable|array',
            'usage_limit' => 'nullable|integer|min:1'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Map frontend field names to database field names
        $data = $request->all();
        if (isset($data['minimum_quantity'])) {
            $data['min_quantity'] = $data['minimum_quantity'];
            unset($data['minimum_quantity']);
        }
        if (isset($data['minimum_amount'])) {
            $data['min_amount'] = $data['minimum_amount'];
            unset($data['minimum_amount']);
        }
        if (isset($data['maximum_discount'])) {
            $data['max_discount'] = $data['maximum_discount'];
            unset($data['maximum_discount']);
        }

        $promotion->update($data);

        return redirect()->route('promotions.index')
                        ->with('success', 'โปรโมชั่นถูกอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Promotion $promotion)
    {
        $promotion->delete();

        return redirect()->route('promotions.index')
                        ->with('success', 'โปรโมชั่นถูกลบเรียบร้อยแล้ว');
    }

    /**
     * Get active promotions for POS
     */
    public function getActivePromotions()
    {
        $promotions = Promotion::active()->get();
        
        return response()->json($promotions);
    }

    /**
     * Apply promotion to cart
     */
    public function applyPromotion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'promotion_id' => 'required|exists:promotions,id',
            'cart_items' => 'required|array',
            'cart_total' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'ข้อมูลไม่ถูกต้อง'], 400);
        }

        $promotion = Promotion::find($request->promotion_id);
        
        if (!$promotion->isValid()) {
            return response()->json(['error' => 'โปรโมชั่นไม่สามารถใช้งานได้'], 400);
        }

        $discount = 0;
        $applicableItems = [];

        foreach ($request->cart_items as $item) {
            if ($promotion->canApplyToProduct($item['product_id'])) {
                $applicableItems[] = $item;
            }
        }

        if (empty($applicableItems)) {
            return response()->json(['error' => 'ไม่มีสินค้าที่สามารถใช้โปรโมชั่นได้'], 400);
        }

        $discount = $promotion->calculateDiscount($applicableItems, $request->cart_total);

        return response()->json([
            'discount' => $discount,
            'applicable_items' => $applicableItems,
            'promotion' => $promotion
        ]);
    }
}
