<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminSaleController;
use App\Http\Controllers\Admin\AdminBranchController;
use App\Http\Controllers\Admin\AdminEmployeeController;
use App\Http\Controllers\Admin\AdminCarModelController;
use App\Http\Controllers\Admin\AdminGlassPositionController;
use App\Http\Controllers\Admin\AdminWithdrawalController;
use App\Http\Controllers\Employee\EmployeeDashboardController;
use App\Http\Controllers\Employee\ItemController;
use App\Http\Controllers\Employee\SaleController;
use App\Http\Controllers\Employee\ExternalSaleController;
use App\Http\Controllers\Employee\ReportController;
use Illuminate\Support\Facades\Auth;

Route::get('/test-session', function() {
    $count = \DB::table('sessions')->count();
    $session_id = session()->getId();
    $auth = Auth::guard('admin')->check();
    return response()->json([
        'sessions_in_db' => $count,
        'current_session_id' => $session_id,
        'is_authenticated' => $auth,
        'session_driver' => config('session.driver'),
        'session_cookie' => config('session.cookie'),
        'session_domain' => config('session.domain'),
        'session_same_site' => config('session.same_site'),
        'session_secure' => config('session.secure'),
    ]);
});

Route::get('/debug-error', function() {
    try {
        $sales = \App\Models\Sale::with([
            'branch', 'employee', 'admin', 
            'item.carModel', 'item.glassPosition'
        ])->orderBy('id', 'desc')->paginate(50);
        
        return response()->json([
            'sales_ok' => true,
            'count' => $sales->total()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

Route::get('/debug-items', function() {
    try {
        $items = \App\Models\Item::with([
            'branch', 'carModel', 'glassPosition'
        ])->orderBy('id', 'desc')->paginate(50);
        
        return response()->json([
            'items_ok' => true,
            'count' => $items->total()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
    }
});

Route::get('/debug-sales-view', function() {
    try {
        $sales = \App\Models\Sale::with([
            'branch', 'employee', 'admin', 
            'item.carModel', 'item.glassPosition'
        ])->orderBy('id', 'desc')->paginate(50);
        
        return view('admin.sales.index', compact('sales'));
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
    }
});

Route::get('/debug-items-view', function() {
    try {
        $items = \App\Models\Item::with([
            'branch', 'carModel', 'glassPosition'
        ])->orderBy('id', 'desc')->paginate(50);
        
        return view('admin.items.index', compact('items'));
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
    }
});

Route::get('/debug-view-error', function() {
    try {
        $items = \App\Models\Item::with(['branch', 'carModel', 'glassPosition'])
            ->orderBy('id', 'desc')
            ->paginate(50);
        
        // اختبر كل item
        foreach($items as $item) {
            $test = [
                'id' => $item->id,
                'branch' => $item->branch ? $item->branch->name : 'NULL BRANCH',
                'carModel' => $item->carModel ? $item->carModel->name : 'NULL CARMODEL',
                'glassPosition' => $item->glassPosition ? $item->glassPosition->name : 'NULL POSITION',
                'retail_price' => $item->retail_price,
                'wholesale_price' => $item->wholesale_price,
                'stock_quantity' => $item->stock_quantity,
                'damaged_quantity' => $item->damaged_quantity,
            ];
        }
        
        return response()->json([
            'success' => true,
            'items' => $items->getCollection()->map(function($item) {
                return [
                    'id' => $item->id,
                    'branch' => $item->branch ? $item->branch->name : 'NULL',
                    'carModel' => $item->carModel ? $item->carModel->name : 'NULL',
                    'glassPosition' => $item->glassPosition ? $item->glassPosition->name : 'NULL',
                    'damaged_quantity' => $item->damaged_quantity,
                ];
            })
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => str_replace(base_path(), '', $e->getFile()),
            'line' => $e->getLine(),
        ]);
    }
});

// Authentication Routes
Route::redirect('/', '/login');
Route::get('/login',[AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login',[AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// ADMIN ROUTES
// ==========================================
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Global Sales & Undo Feature
    Route::get('/sales', [AdminSaleController::class, 'index'])->name('sales.index');
    Route::get('/pos', [AdminSaleController::class, 'create'])->name('pos');
    Route::post('/sales', [AdminSaleController::class, 'store'])->name('sales.store');
    Route::post('/sales/{sale}/undo',[AdminSaleController::class, 'undoSale'])->name('sales.undo');
    
    // Global Inventory & Wholesale Pricing
    Route::get('/items', [App\Http\Controllers\Admin\AdminItemController::class, 'index'])->name('items.index');
    Route::get('/items/create', [App\Http\Controllers\Admin\AdminItemController::class, 'create'])->name('items.create');
    Route::post('/items', [App\Http\Controllers\Admin\AdminItemController::class, 'store'])->name('items.store');
    Route::put('/items/{item}/inventory',[App\Http\Controllers\Admin\AdminItemController::class, 'updateInventory'])->name('items.inventory.update');
    Route::put('/items/{item}/retail',[App\Http\Controllers\Admin\AdminItemController::class, 'updateRetail'])->name('items.retail.update');
    Route::put('/items/{item}/wholesale',[App\Http\Controllers\Admin\AdminItemController::class, 'updateWholesale'])->name('items.wholesale.update');

    // Basic CRUD for Setup (Branches, Employees, Cars) would be added here
    Route::resource('branches', AdminBranchController::class);
    Route::resource('employees', AdminEmployeeController::class);
    Route::resource('car-models', AdminCarModelController::class)->only(['index', 'store', 'destroy']);
    Route::resource('glass-positions', AdminGlassPositionController::class)->only(['index', 'store', 'destroy']);

    // Reports export
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');

    // Withdrawals (total sales withdrawal)
    Route::get('/withdrawals', [AdminWithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::post('/withdrawals/full', [AdminWithdrawalController::class, 'withdrawFull'])->name('withdrawals.full');

    // Salaries and Shop Expenses
    Route::get('/expenses', [App\Http\Controllers\Admin\ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [App\Http\Controllers\Admin\ExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('/expenses/{withdrawal}', [App\Http\Controllers\Admin\ExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::post('/expenses/reset', [App\Http\Controllers\Admin\ExpenseController::class, 'resetSalaries'])->name('expenses.reset');
});

// ==========================================
// EMPLOYEE ROUTES (Branch Scoped)
// ==========================================
Route::middleware('auth:employee')->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');

    // Items (Stock)
    Route::resource('items', ItemController::class);
    Route::post('/items/{item}/damaged',[ItemController::class, 'reportDamaged'])->name('items.damaged');

    // Point of Sale (POS)
    Route::get('/pos', [SaleController::class, 'create'])->name('pos');
    Route::post('/sales',[SaleController::class, 'store'])->name('sales.store');
    
    // External Sales
    Route::resource('external-sales', ExternalSaleController::class);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');

    // Withdrawals
    Route::post('/withdrawals', [App\Http\Controllers\Employee\WithdrawalController::class, 'store'])->name('withdrawals.store');
});