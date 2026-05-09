<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string|max:255',
        ], [], ['amount' => 'المبلغ']);

        $employee = Auth::guard('employee')->user();
        $amount = (float) $request->amount;

        // 1. Check if employee has enough remaining salary
        if ($amount > $employee->remaining_salary) {
            return back()->withErrors(['error' => 'المبلغ المطلوب أكبر من الرصيد المتبقي من المرتب.']);
        }

        // 2. Check daily withdrawal limit
        $todayWithdrawalsSum = Withdrawal::where('employeeID', $employee->id)
            ->whereDate('date', today())
            ->sum('amount');

        if (($todayWithdrawalsSum + $amount) > $employee->daily_withdrawal_limit) {
            $availableToday = $employee->daily_withdrawal_limit - $todayWithdrawalsSum;
            return back()->withErrors(['error' => "لقد تجاوزت حد السحب اليومي. المتاح لك اليوم هو: " . number_format($availableToday, 2) . " د.ل"]);
        }

        DB::transaction(function () use ($employee, $amount, $request) {
            // 3. Create withdrawal record
            Withdrawal::create([
                'employeeID' => $employee->id,
                'amount' => $amount,
                'date' => today(),
                'note' => $request->note,
            ]);

            // 4. Deduct from remaining salary
            $employee->decrement('remaining_salary', $amount);
        });

        return back()->with('success', 'تمت عملية السحب بنجاح وخصمها من المرتب.');
    }
}
