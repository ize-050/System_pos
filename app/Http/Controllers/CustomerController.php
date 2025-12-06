<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        // Allow admin, manager, cashier to manage customers
        $this->middleware('role:admin,manager,cashier');
    }

    /**
     * Display a listing of customers
     */
    public function index(Request $request)
    {
        $query = Customer::query()
            ->with(['createdBy', 'sales'])
            ->withCount(['sales'])
            ->when($request->search, function ($query, $search) {
                $query->search($search);
            })
            ->when($request->customer_type, function ($query, $type) {
                $query->where('customer_type', $type);
            })
            ->when($request->is_active, function ($query, $isActive) {
                $query->where('is_active', $isActive);
            })
            ->orderBy('created_at', 'desc');

        $customers = $query->paginate(15)->withQueryString();

        // Calculate statistics
        $stats = [
            'total_customers' => Customer::count(),
            'active_customers' => Customer::where('is_active', true)->count(),
            'total_balance' => Customer::sum('current_balance'),
            'total_credit_limit' => Customer::sum('credit_limit'),
        ];

        return Inertia::render('Customers/Index', [
            'customers' => $customers,
            'filters' => $request->only(['search', 'customer_type', 'is_active']),
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new customer
     */
    public function create()
    {
        return Inertia::render('Customers/Create');
    }

    /**
     * Store a newly created customer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20|unique:customers',
            'line_id' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100|unique:customers',
            'address' => 'nullable|string',
            'company_name' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:20',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|integer|min:0',
            'customer_type' => 'required|in:individual,company,contractor',
            'notes' => 'nullable|string',
        ]);

        // Set default values for nullable numeric fields
        $validated['credit_limit'] = $validated['credit_limit'] ?? 0;
        $validated['payment_terms'] = $validated['payment_terms'] ?? 0;

        // Generate customer code
        $lastCustomer = Customer::latest('id')->first();
        $nextNumber = $lastCustomer ? (intval(substr($lastCustomer->customer_code, 4)) + 1) : 1;
        $validated['customer_code'] = 'CUST' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        $validated['created_by'] = Auth::id();

        $customer = Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'ลูกค้าถูกสร้างเรียบร้อยแล้ว');
    }

    /**
     * Display the specified customer
     */
    public function show(Customer $customer)
    {
        $customer->load([
            'createdBy',
            'sales' => function ($query) {
                $query->with(['saleItems.product', 'cashier'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10);
            }
        ]);

        // Calculate customer statistics
        $lastPurchase = $customer->sales()->latest()->first();
        
        // Get paginated sales
        $sales = $customer->sales()
            ->with(['saleItems.product', 'cashier'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $totalSales = $customer->sales()->count();
        $totalAmount = $customer->sales()->sum('total_amount');
        
        $statistics = [
            'total_sales' => $totalSales,
            'total_amount' => $totalAmount,
            'total_purchases' => $totalAmount,
            'total_orders' => $totalSales,
            'average_order' => $totalSales > 0 ? $totalAmount / $totalSales : 0,
            'average_per_sale' => $totalSales > 0 ? $totalAmount / $totalSales : 0,
            'last_purchase' => $lastPurchase ? $lastPurchase->created_at->format('d/m/Y H:i') : null,
        ];

        return Inertia::render('Customers/Show', [
            'customer' => $customer,
            'sales' => $sales,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show the form for editing the specified customer
     */
    public function edit(Customer $customer)
    {
        return Inertia::render('Customers/Edit', [
            'customer' => $customer,
        ]);
    }

    /**
     * Update the specified customer
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => ['required', 'string', 'max:20', Rule::unique('customers')->ignore($customer->id)],
            'line_id' => 'nullable|string|max:100',
            'email' => ['nullable', 'email', 'max:100', Rule::unique('customers')->ignore($customer->id)],
            'address' => 'nullable|string',
            'company_name' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:20',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|integer|min:0',
            'customer_type' => 'required|in:individual,company,contractor',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Set default values for nullable numeric fields
        $validated['credit_limit'] = $validated['credit_limit'] ?? 0;
        $validated['payment_terms'] = $validated['payment_terms'] ?? 0;

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'ข้อมูลลูกค้าถูกอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified customer
     */
    public function destroy(Customer $customer)
    {
        // Soft delete - just mark as inactive instead of hard delete
        // This preserves sales history
        if ($customer->sales()->count() > 0) {
            $customer->update(['is_active' => false]);
            return redirect()->route('customers.index')
                ->with('success', 'ลูกค้าถูกปิดการใช้งานเรียบร้อยแล้ว (มีประวัติการซื้อ)');
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'ลูกค้าถูกลบเรียบร้อยแล้ว');
    }

    /**
     * Search customers for AJAX requests
     */
    public function search(Request $request)
    {
        $customers = Customer::query()
            ->where('is_active', true)
            ->when($request->search, function ($query, $search) {
                $query->search($search);
            })
            ->limit(10)
            ->get(['id', 'customer_code', 'name', 'phone', 'line_id', 'current_balance', 'credit_limit']);

        return response()->json($customers);
    }
}