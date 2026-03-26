<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExternalSale;
use Illuminate\Support\Facades\Auth;

class ExternalSaleController extends Controller
{
    public function index() { return view('employee.external-sales.index'); }
    public function store(Request $request)
    {
        $request->validate([
            'sale_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0'
        ], [], ['sale_type' => 'نوع البيع', 'amount' => 'القيمة']);

        $employee = Auth::guard('employee')->user();

        ExternalSale::create([
            'branchID' => $employee->branchID,
            'employeeID' => $employee->id,
            'sale_type' => $request->sale_type,
            'amount' => $request->amount,
        ]);

        return redirect()->back()->with('success', 'تم إضافة البيع الخارجي بنجاح.');
    }
}