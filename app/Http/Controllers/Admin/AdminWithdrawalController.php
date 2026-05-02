<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\ExternalSale;
use App\Models\AdminWithdrawal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminWithdrawalController extends Controller
{
    // Show list of withdrawals (simple)
    public function index()
    {
        $withdrawals = AdminWithdrawal::with('admin')->orderBy('id', 'desc')->paginate(30);
        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    // Withdraw the full available amount (total sales + external sales - previous withdrawals)
    public function withdrawFull(Request $request)
    {
        $available = $this->calculateAvailableAmount();

        if ($available <= 0) {
            return back()->withErrors(['error' => 'لا يوجد مبلغ متاح للسحب حالياً.']);
        }

        DB::transaction(function () use ($available, $request) {
            AdminWithdrawal::create([
                'adminID' => Auth::guard('admin')->id(),
                'amount' => $available,
                'note' => $request->input('note'),
                'withdrawn_at' => now(),
            ]);
        });

        return redirect()->route('admin.dashboard')->with('success', 'تم سحب كامل المبلغ المتاح بنجاح: ' . number_format($available, 2) . ' دينار');
    }

    protected function calculateAvailableAmount()
    {
        $salesTotal = Sale::where('status', 'completed')->sum('sold_price');
        $externalTotal = ExternalSale::sum('amount');
        $withdrawn = AdminWithdrawal::sum('amount');

        return round(($salesTotal + $externalTotal) - $withdrawn, 2);
    }
}
