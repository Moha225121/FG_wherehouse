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
    public function index(Request $request)
    {
        $query = Item::with(['branch', 'carModel', 'glassPosition'])->orderBy('shelf_number', 'asc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('shelf_number', 'like', "%{$search}%")
                    ->orWhere('glass_type', 'like', "%{$search}%")
                    ->orWhereHas('branch', fn ($sq) => $sq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('carModel', fn ($sq) => $sq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('glassPosition', fn ($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }
        $query->when($request->filled('branchID'), fn ($q) => $q->where('branchID', $request->branchID));
        $query->when($request->filled('carModelID'), fn ($q) => $q->where('carModelID', $request->carModelID));
        $query->when($request->filled('glassPositionID'), fn ($q) => $q->where('glassPositionID', $request->glassPositionID));
        if ($request->stock_status === 'available') $query->where('stock_quantity', '>', 0);
        if ($request->stock_status === 'out') $query->where('stock_quantity', '<=', 0);
        if ($request->stock_status === 'low') $query->whereBetween('stock_quantity', [1, 2]);

        $items = $query->paginate(50)->withQueryString();
        $branches = Branch::orderBy('name')->get();
        $carModels = CarModel::orderBy('name')->get();
        $glassPositions = GlassPosition::orderBy('name')->get();

        return view('admin.items.index', compact('items', 'branches', 'carModels', 'glassPositions'));
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

    public function updateInventory(Request $request, Item $item)
    {
        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
            'damaged_quantity' => 'required|integer|min:0',
            'shelf_number' => 'nullable|string|max:50',
        ], [], [
            'stock_quantity' => 'الكمية المتوفرة',
            'damaged_quantity' => 'المخزون التالف',
            'shelf_number' => 'مكان الطارمة',
        ]);

        $item->update([
            'stock_quantity' => $request->stock_quantity,
            'damaged_quantity' => $request->damaged_quantity,
            'shelf_number' => $request->shelf_number,
        ]);

        return back()->with('success', 'تم تحديث الكمية والتالف ومكان الطارمة بنجاح.');
    }
}
