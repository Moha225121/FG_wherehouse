<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Branch;
use App\Models\CarModel;
use App\Models\GlassPosition;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminItemController extends Controller
{
    public function index()
    {
        $items = Item::with(['branch', 'carModel', 'glassPosition'])
                     ->orderBy('id', 'desc')
                     ->paginate(50);

        return view('admin.items.index', compact('items'));
    }

    public function create()
    {
        $branches = Branch::orderBy('name')->get();
        $carModels = CarModel::orderBy('name')->get();
        $glassPositions = GlassPosition::orderBy('name')->get();

        return view('admin.items.create', compact('branches', 'carModels', 'glassPositions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branchID' => 'required|exists:branches,id',
            'carModelID' => 'required|exists:car_models,id',
            'glassPositionID' => 'required|exists:glass_positions,id',
            'glass_type' => 'nullable|string|max:255',
            'shelf_number' => 'nullable|string|max:50',
            'retail_price' => 'required|numeric|min:0',
            'wholesale_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:1',
        ], [], [
            'branchID' => 'الفرع',
            'carModelID' => 'نوع السيارة',
            'glassPositionID' => 'موقع الزجاج',
            'retail_price' => 'سعر البيع',
            'wholesale_price' => 'سعر الجملة',
            'stock_quantity' => 'الكمية',
        ]);

        DB::transaction(function () use ($request) {
            $item = Item::create([
                'branchID' => $request->branchID,
                'carModelID' => $request->carModelID,
                'glassPositionID' => $request->glassPositionID,
                'glass_type' => $request->glass_type,
                'shelf_number' => $request->shelf_number,
                'retail_price' => $request->retail_price,
                'wholesale_price' => $request->wholesale_price,
                'stock_quantity' => $request->stock_quantity,
            ]);

            StockMovement::create([
                'itemID' => $item->id,
                'employeeID' => null,
                'adminID' => Auth::guard('admin')->id(),
                'movement_type' => 'add',
                'quantity' => $request->stock_quantity,
                'note' => 'إضافة مخزون بواسطة الإدارة',
            ]);
        });

        return redirect()->route('admin.items.index')->with('success', 'تمت إضافة الصنف إلى المخزون بنجاح.');
    }

    public function updateRetail(Request $request, Item $item)
    {
        $request->validate([
            'retail_price' => 'required|numeric|min:0',
        ], [], ['retail_price' => 'سعر البيع للموظف']);

        $item->update([
            'retail_price' => $request->retail_price,
        ]);

        return back()->with('success', 'تم تحديث سعر البيع للموظف بنجاح.');
    }

    public function updateWholesale(Request $request, Item $item)
    {
        $request->validate([
            'wholesale_price' => 'required|numeric|min:0'
        ], [], ['wholesale_price' => 'سعر الجملة']);

        $item->update([
            'wholesale_price' => $request->wholesale_price
        ]);

        return back()->with('success', 'تم تحديث سعر الجملة للصنف بنجاح.');
    }
}