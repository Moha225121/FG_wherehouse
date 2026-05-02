<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\ExternalSale;
use App\Models\Branch;
use App\Models\AdminWithdrawal;

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

        // Available amount for withdrawal = all completed sales + external sales - previous withdrawals
        $salesTotal = Sale::where('status', 'completed')->sum('sold_price');
        $externalTotal = ExternalSale::sum('amount');
        $withdrawn = AdminWithdrawal::sum('amount');

        $availableAmount = round(($salesTotal + $externalTotal) - $withdrawn, 2);

        // Last withdrawal time
        $lastWithdrawal = AdminWithdrawal::orderBy('withdrawn_at', 'desc')->first();

        return view('admin.dashboard', compact('totalDailyRevenue', 'todaySales', 'todayExternalSales', 'branchesCount', 'availableAmount', 'lastWithdrawal'));
    }
}