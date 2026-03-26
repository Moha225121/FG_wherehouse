<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\ExternalSale;
use App\Models\Branch;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Global Daily Sales (Completed normal sales + External sales)
        $todaySales = Sale::whereDate('created_at', today())
                          ->where('status', 'completed')
                          ->sum('sold_price');
                          
        $todayExternalSales = ExternalSale::whereDate('created_at', today())
                                          ->sum('amount');
                                          
        $totalDailyRevenue = $todaySales + $todayExternalSales;

        // Count of active branches for the dashboard
        $branchesCount = Branch::count();

        return view('admin.dashboard', compact('totalDailyRevenue', 'todaySales', 'todayExternalSales', 'branchesCount'));
    }
}