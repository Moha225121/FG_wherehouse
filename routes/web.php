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

    // Withdrawals (full-fee withdrawal)
    Route::get('/withdrawals', [AdminWithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::post('/withdrawals/full', [AdminWithdrawalController::class, 'withdrawFull'])->name('withdrawals.full');
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
});