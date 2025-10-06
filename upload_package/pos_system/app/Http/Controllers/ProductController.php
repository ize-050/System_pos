<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('role:admin,manager')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->when($request->search, function ($query, $search) {
                $query->search($search);
            })
            ->when($request->category_id, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($request->status, function ($query, $status) {
                if ($status === 'active') {
                    $query->active();
                } elseif ($status === 'inactive') {
                    $query->where('is_active', false);
                } elseif ($status === 'low_stock') {
                    $query->lowStock();
                } elseif ($status === 'out_of_stock') {
                    $query->outOfStock();
                }
            })
            ->when($request->sort, function ($query, $sort) {
                if ($sort === 'name') {
                    $query->orderBy('name');
                } elseif ($sort === 'price') {
                    $query->orderBy('selling_price');
                } elseif ($sort === 'stock') {
                    $query->orderBy('current_stock');
                } else {
                    $query->latest();
                }
            }, function ($query) {
                $query->latest();
            });

        $products = $query->paginate(15)->appends($request->query());
        $categories = ProductCategory::active()->get();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category_id', 'status', 'sort'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = ProductCategory::active()->get();
        
        return Inertia::render('Products/Create', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $validated = $request->validated();

            if ($request->hasFile('image')) {
                $validated['image_path'] = $request->file('image')->store('products', 'public');
            }

            Product::create($validated);

            return redirect()->route('products.index')
                ->with('success', 'เพิ่มสินค้าเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาดในการเพิ่มสินค้า: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $product->load('category');
        
        return Inertia::render('Products/Show', [
            'product' => $product
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $categories = ProductCategory::active()->get();
        
        return Inertia::render('Products/Edit', [
            'product' => $product,
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
    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            $validated = $request->validated();

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($product->image_path) {
                    Storage::disk('public')->delete($product->image_path);
                }
                $validated['image_path'] = $request->file('image')->store('products', 'public');
            }

            $product->update($validated);

            return redirect()->route('products.index')
                ->with('success', 'แก้ไขสินค้าเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาดในการแก้ไขสินค้า: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        try {
            // Check if product has sales history
            if ($product->saleItems()->exists()) {
                // Instead of deleting, deactivate the product
                $product->update(['is_active' => false]);
                
                return redirect()->route('products.index')
                    ->with('success', 'ไม่สามารถลบสินค้าที่มีประวัติการขายได้ ระบบได้ปิดการใช้งานสินค้านี้แล้ว');
            }
            
            // If no sales history, allow deletion
            // Delete image if exists
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            
            $product->delete();

            return redirect()->route('products.index')
                ->with('success', 'ลบสินค้าเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'เกิดข้อผิดพลาดในการลบสินค้า: ' . $e->getMessage());
        }
    }

    /**
     * Search products
     */
    public function search(Request $request)
    {
        $query = Product::with('category')
            ->when($request->q, function ($query, $search) {
                $query->search($search);
            })
            ->active()
            ->limit(10);

        return response()->json($query->get());
    }

    /**
     * Restore soft deleted product
     */
    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();

        return redirect()->route('products.index')
            ->with('success', 'Product restored successfully.');
    }
}
