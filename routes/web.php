<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReceiptSettingsController;
use App\Http\Controllers\PurchaseReportController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\DailyReportController;
use App\Exports\PurchaseOrdersExport;
use Maatwebsite\Excel\Facades\Excel;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/test', function () {
    return Inertia::render('Test');
});

Route::get('welcome',function(){
    return view('welcome');
});

Route::get('/debug', function () {
    return Inertia::render('Debug');
});

// Protected routes
Route::middleware(['auth', 'verified'])->group(function () {
    // User management (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
        Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::resource('users', UserController::class);
    });

    // Product management (Admin and Manager)
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
        Route::post('/products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::get('/products/template/download', [ProductController::class, 'downloadTemplate'])->name('products.template.download');
        Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
        Route::resource('products', ProductController::class);
    });

    // Stock/Inventory Management Routes (Admin and Manager)
    Route::middleware(['role:admin,manager'])->group(function () {
        Route::get('/stocks/low-stock', [StockController::class, 'getLowStockProducts'])->name('stocks.low-stock');
        Route::post('/stocks/bulk-adjustment', [StockController::class, 'bulkAdjustment'])->name('stocks.bulk-adjustment');
        Route::resource('stocks', StockController::class);
    });

    // Category Management Routes (Admin and Manager only)
    Route::middleware(['role:admin,manager'])->group(function () {
        Route::get('/categories/search', [CategoryController::class, 'search'])->name('categories.search');
        Route::get('/categories/template/download', [CategoryController::class, 'downloadTemplate'])->name('categories.template.download');
        Route::post('/categories/import', [CategoryController::class, 'import'])->name('categories.import');
        Route::resource('categories', CategoryController::class);
    });

    // Customer Management Routes (Admin, Manager, and Cashier)
    Route::middleware(['role:admin,manager,cashier'])->group(function () {
        Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
        Route::resource('customers', CustomerController::class);
    });

    // Sales Management Routes (Admin, Manager, and Cashier)
    Route::middleware(['role:admin,manager,cashier'])->group(function () {
        Route::get('/sales/search-products', [SaleController::class, 'searchProducts'])->name('sales.search-products');
        Route::get('/sales/{sale}/receipt', [SaleController::class, 'printReceipt'])->name('sales.receipt');
        Route::resource('sales', SaleController::class);
    });

    // Promotion routes
    Route::resource('promotions', PromotionController::class);
Route::get('/api/promotions/active', [PromotionController::class, 'getActivePromotions'])->name('promotions.active');
Route::post('/api/promotions/apply', [PromotionController::class, 'applyPromotion'])->name('promotions.apply');

// Debtors/Creditors routes
Route::get('/debtors', function () {
    $customers = \App\Models\Customer::where('current_balance', '>', 0)
        ->orderBy('current_balance', 'desc')
        ->get();

    return Inertia::render('Debtors/Index', [
        'customers' => $customers
    ]);
})->name('debtors.index');

    // POS System Routes
    Route::middleware(['role:admin,manager,cashier'])->group(function () {
        Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
        Route::get('/pos/search-products', [POSController::class, 'searchProducts'])->name('pos.search-products');
        Route::get('/pos/search-customers', [POSController::class, 'searchCustomers'])->name('pos.search-customers');
        Route::get('/pos/barcode/{barcode}', [POSController::class, 'getProductByBarcode'])->name('pos.barcode');
        Route::post('/pos/process-sale', [POSController::class, 'processSale'])->name('pos.process-sale');
        Route::get('/pos/daily-summary', [POSController::class, 'getDailySummary'])->name('pos.daily-summary');
        Route::post('/pos/promotions/active', [POSController::class, 'getActivePromotions'])->name('pos.promotions.active');
        Route::post('/pos/promotions/apply', [POSController::class, 'applyPromotion'])->name('pos.promotions.apply');
    });

    // Dashboard and Reports Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports/sales', [DashboardController::class, 'salesReport'])->name('reports.sales');
    Route::get('/reports/products', [DashboardController::class, 'productReport'])->name('reports.products');
    Route::get('/reports/inventory', [DashboardController::class, 'inventoryReport'])->name('reports.inventory');

    // Analytics routes
    Route::middleware(['role:admin,manager'])->group(function () {
        Route::get('/analytics', function () {
            return Inertia::render('Analytics');
        })->name('analytics.index');
        Route::get('/api/analytics/comprehensive', [DashboardController::class, 'comprehensiveAnalytics'])->name('api.analytics.comprehensive');
        Route::get('/api/analytics/export', [DashboardController::class, 'exportAnalytics'])->name('api.analytics.export');
    });

    // Purchase Order Routes (Admin, Manager, Accountant)
    Route::middleware('role:admin,manager,accountant')->group(function () {
        Route::get('/purchase-orders/export', function () {
            return Excel::download(new PurchaseOrdersExport, 'purchase-orders.xlsx');
        })->name('purchase-orders.export');
        
        Route::post('/purchase-orders/{id}/send', [PurchaseOrderController::class, 'sendOrder'])->name('purchase-orders.send');
        Route::get('/purchase-orders/{id}/receive', [PurchaseOrderController::class, 'receiveGoods'])->name('purchase-orders.receive');
        Route::post('/purchase-orders/{id}/receive', [PurchaseOrderController::class, 'storeReceivedGoods'])->name('purchase-orders.receive.store');
        
        Route::resource('purchase-orders', PurchaseOrderController::class);
    });

    // Receipt Routes (All authenticated users)
    Route::prefix('receipts')->group(function () {
        Route::get('/{saleId}/preview', [ReceiptController::class, 'preview'])->name('receipts.preview');
        Route::get('/{saleId}/print', [ReceiptController::class, 'print'])->name('receipts.print');
        Route::get('/{saleId}/download-pdf', [ReceiptController::class, 'downloadPDF'])->name('receipts.download-pdf');
        Route::post('/{saleId}/send-email', [ReceiptController::class, 'sendEmail'])->name('receipts.send-email');
    });

    // Receipt Settings Routes (Admin, Manager)
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/receipt-settings', [ReceiptSettingsController::class, 'edit'])->name('receipt-settings.edit');
        Route::post('/receipt-settings', [ReceiptSettingsController::class, 'update'])->name('receipt-settings.update');
    });

    // Advanced Reporting & Analytics Routes (Admin, Manager, Accountant)
    Route::middleware('role:admin,manager,accountant')->group(function () {
        // Purchase Report Routes (T038)
        Route::get('/reports/purchases', [PurchaseReportController::class, 'index'])->name('reports.purchases');
        Route::post('/reports/purchases/export', [PurchaseReportController::class, 'export'])->name('reports.purchases.export');
        
        // Export Routes (T040)
        Route::post('/reports/export', [ExportController::class, 'export'])->name('reports.export');
        Route::get('/reports/export/{id}/download', [ExportController::class, 'download'])->name('reports.export.download');
        Route::get('/reports/export/{id}/status', [ExportController::class, 'status'])->name('reports.export.status');
        
        // Daily Report Routes (T041)
        Route::get('/reports/daily', [DailyReportController::class, 'index'])->name('reports.daily');
        Route::get('/reports/daily/{date}', [DailyReportController::class, 'show'])->name('reports.daily.show');
        Route::post('/reports/daily/generate', [DailyReportController::class, 'generate'])->name('reports.daily.generate');
        Route::post('/reports/daily/export', [DailyReportController::class, 'export'])->name('reports.daily.export');
    });

// Profile Routes
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
