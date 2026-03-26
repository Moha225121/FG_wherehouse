<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GlassPosition;

class AdminGlassPositionController extends Controller
{
    public function index() {
        $positions = GlassPosition::all();
        return view('admin.glass_positions.index', compact('positions'));
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required|string|max:255']);
        GlassPosition::create($request->only('name'));
        return back()->with('success', 'تم إضافة موقع الزجاج بنجاح.');
    }

    public function destroy(GlassPosition $glassPosition) {
        $glassPosition->delete();
        return back()->with('success', 'تم الحذف بنجاح.');
    }
}