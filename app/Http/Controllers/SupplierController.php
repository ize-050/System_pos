<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('role:admin,manager,accountant');
    }

    /**
     * Display a listing of suppliers
     */
    public function index(Request $request)
    {
        $query = Supplier::query()
            ->when($request->search, function ($query, $search) {
                $query->search($search);
            })
            ->when($request->is_active !== null && $request->is_active !== '', function ($query) use ($request) {
                $query->where('is_active', $request->is_active);
            })
            ->orderBy('created_at', 'desc');

        $suppliers = $query->paginate(15)->withQueryString();

        // Calculate statistics
        $stats = [
            'total_suppliers' => Supplier::count(),
            'active_suppliers' => Supplier::where('is_active', true)->count(),
            'inactive_suppliers' => Supplier::where('is_active', false)->count(),
        ];

        return Inertia::render('Suppliers/Index', [
            'suppliers' => $suppliers,
            'filters' => $request->only(['search', 'is_active']),
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new supplier
     */
    public function create()
    {
        return Inertia::render('Suppliers/Create');
    }

    /**
     * Store a newly created supplier
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'contact_person' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100|unique:suppliers',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = true;

        Supplier::create($validated);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'เพิ่มซัพพลายเออร์เรียบร้อยแล้ว');
    }

    /**
     * Display the specified supplier
     */
    public function show(Supplier $supplier)
    {
        return Inertia::render('Suppliers/Show', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Show the form for editing the specified supplier
     */
    public function edit(Supplier $supplier)
    {
        return Inertia::render('Suppliers/Edit', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Update the specified supplier
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'contact_person' => 'nullable|string|max:100',
            'email' => ['nullable', 'email', 'max:100', Rule::unique('suppliers')->ignore($supplier->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $supplier->update($validated);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'อัปเดตข้อมูลซัพพลายเออร์เรียบร้อยแล้ว');
    }

    /**
     * Remove the specified supplier
     */
    public function destroy(Supplier $supplier)
    {
        // Mark as inactive (soft delete)
        $supplier->is_active = false;
        $supplier->save();

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'ปิดการใช้งานซัพพลายเออร์เรียบร้อยแล้ว');
    }

    /**
     * Search suppliers for AJAX requests
     */
    public function search(Request $request)
    {
        $suppliers = Supplier::query()
            ->where('is_active', true)
            ->when($request->search, function ($query, $search) {
                $query->search($search);
            })
            ->limit(10)
            ->get(['id', 'name', 'contact_person', 'phone', 'email']);

        return response()->json($suppliers);
    }
}
