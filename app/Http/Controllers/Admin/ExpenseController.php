<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Withdrawal;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $this->autoResetSalaries();

        $query = Withdrawal::with('employee')->orderBy('id', 'desc');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('employeeID')) {
            $query->where('employeeID', $request->employeeID);
        }

        $withdrawals = $query->paginate(50);
        $employees = Employee::orderBy('name')->get();

        return view('admin.expenses.index', compact('withdrawals', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:salary,expense',
            'amount' => 'required|numeric|min:0.01',
            'employeeID' => 'required_if:type,salary|nullable|exists:employees,id',
            'note' => 'nullable|string|max:255',
            'date' => 'required|date',
        ], [], [
            'type' => 'نوع السحب',
            'amount' => 'المبلغ',
            'employeeID' => 'الموظف',
            'date' => 'التاريخ'
        ]);

        $amount = (float) $request->amount;

        if ($request->type === 'salary') {
            $employee = Employee::findOrFail($request->employeeID);
            
            // Check daily limit and balance logic? 
            // Since Admin is doing it, we might skip the hard limit or just warn, 
            // but the user said "records the withdrawn amount", so we should deduct it.
            
            DB::transaction(function () use ($employee, $amount, $request) {
                Withdrawal::create([
                    'type' => 'salary',
                    'employeeID' => $employee->id,
                    'amount' => $amount,
                    'date' => $request->date,
                    'note' => $request->note,
                ]);

                $employee->decrement('remaining_salary', $amount);
            });
        } else {
            // Shop Expense
            Withdrawal::create([
                'type' => 'expense',
                'employeeID' => null,
                'amount' => $amount,
                'date' => $request->date,
                'note' => $request->note,
            ]);
        }

        return redirect()->back()->with('success', 'تم تسجيل السحب بنجاح.');
    }

    private function autoResetSalaries()
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $today = Carbon::now()->day;

        // Reset for employees whose reset day has come and haven't been reset this month
        $employeesToReset = Employee::where('last_reset_month', '!=', $currentMonth)
            ->orWhereNull('last_reset_month')
            ->get();

        foreach ($employeesToReset as $employee) {
            if ($today >= $employee->salary_reset_day) {
                $employee->update([
                    'remaining_salary' => $employee->salary,
                    'last_reset_month' => $currentMonth
                ]);
            }
        }
    }

    public function resetSalaries()
    {
        // This is a manual reset button for the admin
        $currentMonth = Carbon::now()->format('Y-m');
        $today = Carbon::now()->day;
        
        $employeesToReset = Employee::where('salary_reset_day', $today)->get();
        
        $count = 0;
        foreach ($employeesToReset as $employee) {
            $employee->update([
                'remaining_salary' => $employee->salary,
                'last_reset_month' => $currentMonth
            ]);
            $count++;
        }

        return redirect()->back()->with('success', "تم تصفير مرتبات $count موظفاً يدوياً (يوم التصفية هو اليوم).");
    }

    public function destroy(Withdrawal $withdrawal)
    {
        DB::transaction(function () use ($withdrawal) {
            if ($withdrawal->type === 'salary' && $withdrawal->employeeID) {
                $employee = Employee::find($withdrawal->employeeID);
                if ($employee) {
                    $employee->increment('remaining_salary', $withdrawal->amount);
                }
            }
            $withdrawal->delete();
        });

        return redirect()->back()->with('success', 'تم حذف السجل وإرجاع المبلغ للمرتب (إذا كان سحب مرتب).');
    }
}
