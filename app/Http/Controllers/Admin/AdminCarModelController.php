<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarModel;

class AdminCarModelController extends Controller
{
    public function index() {
        $carModels = CarModel::orderBy('id', 'desc')->get();
        return view('admin.car_models.index', compact('carModels'));
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required|string|max:255', 'note' => 'nullable|string']);
        CarModel::create($request->only(['name', 'note']));
        return back()->with('success', 'تم إضافة نوع السيارة بنجاح.');
    }

    public function destroy(CarModel $carModel) {
        // Here you might want to check if it's linked to items before deleting, but keeping it simple
        $carModel->delete();
        return back()->with('success', 'تم حذف نوع السيارة بنجاح.');
    }
}