<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Sale;
use App\Models\ExternalSale;
use App\Models\Item;

class EmployeeDashboardController extends Controller
{
    public function index()
    {
        $branchID = Auth::guard('employee')->user()->branchID;

        // Branch-specific daily sales
        $todaySales = Sale::where('branchID', $branchID)
                          ->whereDate('created_at', today())
                          ->where('status', 'completed')
                          ->sum('sold_price');

        $todayExternalSales = ExternalSale::where('branchID', $branchID)
                                          ->whereDate('created_at', today())
                                          ->sum('amount');

        $totalDailyRevenue = $todaySales + $todayExternalSales;

        // Low stock alerts for this branch (e.g., stock less than 3)
        $lowStockItems = Item::with(['carModel', 'glassPosition'])
                             ->where('branchID', $branchID)
                             ->where('stock_quantity', '<', 3)
                             ->get();

        return view('employee.dashboard', compact('totalDailyRevenue', 'todaySales', 'todayExternalSales', 'lowStockItems'));
    }
}