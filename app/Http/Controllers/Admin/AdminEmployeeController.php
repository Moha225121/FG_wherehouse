<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;

class AdminEmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('branch')->orderBy('id', 'desc')->get();
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        $branches = Branch::all();
        if($branches->isEmpty()) {
            return redirect()->route('admin.branches.index')->withErrors(['error' => 'يجب إضافة فرع واحد على الأقل قبل إضافة الموظفين.']);
        }
        return view('admin.employees.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branchID' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:employees,username',
            'password' => 'required|string|min:6',
            'status' => 'required|in:active,inactive',
        ], [],['branchID' => 'الفرع', 'name' => 'اسم الموظف', 'username' => 'اسم المستخدم', 'password' => 'كلمة المرور']);

        Employee::create([
            'branchID' => $request->branchID,
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password), // Hashing the password
            'status' => $request->status,
        ]);

        return redirect()->route('admin.employees.index')->with('success', 'تم إضافة الموظف بنجاح.');
    }

    public function edit(Employee $employee)
    {
        $branches = Branch::all();
        return view('admin.employees.edit', compact('employee', 'branches'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'branchID' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:employees,username,' . $employee->id,
            'password' => 'nullable|string|min:6', // Optional during update
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->only(['branchID', 'name', 'username', 'status']);
        
        // Only update password if the admin typed a new one
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $employee->update($data);

        return redirect()->route('admin.employees.index')->with('success', 'تم تحديث بيانات الموظف بنجاح.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->back()->with('success', 'تم حذف حساب الموظف بنجاح.');
    }
}