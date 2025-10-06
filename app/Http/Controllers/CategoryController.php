<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CategoryTemplateExport;
use App\Imports\CategoryImport;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProductCategory::query();

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $categories = $query->withCount('products')
                           ->orderBy('name')
                           ->paginate(10)
                           ->withQueryString();

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Categories/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            $validated = $request->validated();

            // Handle image upload
            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('categories', 'public');
            }

            ProductCategory::create($validated);

            return redirect()->route('categories.index')
                            ->with('success', 'หมวดหมู่ถูกสร้างเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'เกิดข้อผิดพลาดในการสร้างหมวดหมู่: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCategory $category)
    {
        $category->load(['products' => function ($query) {
            $query->select('id', 'name', 'sku', 'selling_price', 'current_stock', 'is_active', 'category_id')
                  ->orderBy('name');
        }]);

        return Inertia::render('Categories/Show', [
            'category' => $category,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $category)
    {
        return Inertia::render('Categories/Edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, ProductCategory $category)
    {
        try {
            $validated = $request->validated();

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($category->image) {
                    Storage::disk('public')->delete($category->image);
                }
                $validated['image'] = $request->file('image')->store('categories', 'public');
            }

            $category->update($validated);

            return redirect()->route('categories.index')
                            ->with('success', 'หมวดหมู่ถูกอัปเดตเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'เกิดข้อผิดพลาดในการอัปเดตหมวดหมู่: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $category)
    {
        try {
            // Check if category has products
            if ($category->products()->count() > 0) {
                return redirect()->back()
                                ->with('error', 'ไม่สามารถลบหมวดหมู่ที่มีสินค้าอยู่ได้ กรุณาย้ายหรือลบสินค้าก่อน');
            }

            // Delete image if exists
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $category->delete();

            return redirect()->route('categories.index')
                            ->with('success', 'หมวดหมู่ถูกลบเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'เกิดข้อผิดพลาดในการลบหมวดหมู่: ' . $e->getMessage());
        }
    }

    /**
     * Search categories for AJAX requests
     */
    public function search(Request $request)
    {
        $query = ProductCategory::query();

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%')
                  ->where('is_active', true);
        }

        $categories = $query->select('id', 'name')
                           ->orderBy('name')
                           ->limit(10)
                           ->get();

        return response()->json($categories);
    }

    /**
     * API endpoint for categories (for POS system)
     */
    public function apiIndex(Request $request)
    {
        $query = ProductCategory::where('is_active', true);

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $categories = $query->withCount(['products' => function ($q) {
                                $q->where('is_active', true)
                                  ->where('current_stock', '>', 0);
                            }])
                           ->orderBy('name')
                           ->get();

        return response()->json([
            'data' => $categories
        ]);
    }

    /**
     * Download Excel template for category import
     */
    public function downloadTemplate()
    {
        return Excel::download(new CategoryTemplateExport, 'category_template.xlsx');
    }

    /**
     * Import categories from Excel file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ], [
            'file.required' => 'กรุณาเลือกไฟล์ Excel',
            'file.mimes' => 'ไฟล์ต้องเป็นนามสกุล .xlsx, .xls หรือ .csv เท่านั้น',
            'file.max' => 'ขนาดไฟล์ต้องไม่เกิน 2MB'
        ]);

        try {
            $import = new CategoryImport;
            Excel::import($import, $request->file('file'));

            $successCount = $import->getSuccessCount();
            $errorCount = $import->getErrorCount();

            if ($import->hasErrors()) {
                $errors = $import->getErrors();
                return back()->with('error', 'นำเข้าข้อมูลสำเร็จ ' . $successCount . ' รายการ แต่มีข้อผิดพลาด ' . $errorCount . ' รายการ: ' . implode(', ', array_slice($errors, 0, 3)));
            }

            return back()->with('success', 'นำเข้าข้อมูลหมวดหมู่สำเร็จ ' . $successCount . ' รายการ');

        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาดในการนำเข้าข้อมูล: ' . $e->getMessage());
        }
    }
}
