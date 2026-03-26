<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;

class AdminBranchController extends Controller
{
    public function index()
    {
        $branches = Branch::withCount('employees')->orderBy('id', 'desc')->get();
        return view('admin.branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ], [],['name' => 'اسم الفرع', 'location' => 'الموقع']);

        Branch::create($request->only(['name', 'location']));

        return redirect()->back()->with('success', 'تم إضافة الفرع بنجاح.');
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        $branch->update($request->only(['name', 'location']));

        return redirect()->back()->with('success', 'تم تعديل بيانات الفرع بنجاح.');
    }

    public function destroy(Branch $branch)
    {
        if ($branch->employees()->count() > 0) {
            return redirect()->back()->withErrors(['error' => 'لا يمكن حذف هذا الفرع لارتباط موظفين به.']);
        }
        
        $branch->delete();
        return redirect()->back()->with('success', 'تم حذف الفرع بنجاح.');
    }
}