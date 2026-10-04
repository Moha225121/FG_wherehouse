<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\CarModel;
use App\Models\GlassPosition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $branchID = Auth::guard('employee')->user()->branchID;
        
        $query = Item::with(['carModel', 'glassPosition'])
                     ->where('branchID', $branchID)
                     ->orderBy('id', 'desc');
        if ($request->filled('search')) { $s = $request->search; $query->where(function($q) use ($s) { $q->where('shelf_number','like',"%$s%")->orWhere('glass_type','like',"%$s%")->orWhereHas('carModel',fn($x)=>$x->where('name','like',"%$s%"))->orWhereHas('glassPosition',fn($x)=>$x->where('name','like',"%$s%")); }); }
        if ($request->filled('glassPositionID')) $query->where('glassPositionID', $request->glassPositionID);
        $items = $query->get();
        $glassPositions = GlassPosition::orderBy('name')->get();
                     
        return view('employee.items.index', compact('items','glassPositions'));
    }

    public function create()
    {
        $carModels = CarModel::all();
        $glassPositions = GlassPosition::all();
        return view('employee.items.create', compact('carModels', 'glassPositions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'carModelID' => 'required|exists:car_models,id',
            'glassPositionID' => 'required|exists:glass_positions,id',
            'glass_type' => 'nullable|string|max:255',
            'shelf_number' => 'nullable|string|max:50',
            'stock_quantity' => 'required|integer|min:1',
        ],[],[
            'carModelID' => 'نوع السيارة',
            'glassPositionID' => 'موقع الزجاج',
            'stock_quantity' => 'الكمية'
        ]);

        $employee = Auth::guard('employee')->user();

        DB::transaction(function () use ($request, $employee) {
            // 1. Create the item
            $item = Item::create([
                'branchID' => $employee->branchID,
                'carModelID' => $request->carModelID,
                'glassPositionID' => $request->glassPositionID,
                'glass_type' => $request->glass_type,
                'shelf_number' => $request->shelf_number,
                // Employees cannot set pricing. Admin sets retail/wholesale later from central inventory.
                'retail_price' => 0,
                'stock_quantity' => $request->stock_quantity,
            ]);

            // 2. Log the addition in Stock Movements (عدد السحب والاضافة)
            StockMovement::create([
                'itemID' => $item->id,
                'employeeID' => $employee->id,
                'movement_type' => 'add',
                'quantity' => $request->stock_quantity,
                'note' => 'إدخال صنف جديد'
            ]);
        });

        return redirect()->route('employee.items.index')->with('success', 'تم إدخال الصنف الجديد بنجاح.');
    }

    // Report Damaged Item (تسجيل تالف)
    public function reportDamaged(Request $request, Item $item)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string'
        ], [],['quantity' => 'الكمية التالفة']);

        $employee = Auth::guard('employee')->user();

        // Security check: ensure item belongs to employee's branch
        if ($item->branchID !== $employee->branchID) {
            abort(403, 'غير مصرح لك بتعديل هذا الصنف.');
        }

        if ($request->quantity > $item->stock_quantity) {
            return back()->withErrors(['quantity' => 'الكمية التالفة أكبر من المتوفرة في المخزن.']);
        }

        DB::transaction(function () use ($request, $employee, $item) {
            // 1. Deduct from stock
            $item->decrement('stock_quantity', $request->quantity);

            // Keep a running damaged total for admin inventory visibility
            $item->increment('damaged_quantity', $request->quantity);

            // 2. Log as damaged
            StockMovement::create([
                'itemID' => $item->id,
                'employeeID' => $employee->id,
                'movement_type' => 'damaged',
                'quantity' => $request->quantity,
                'note' => $request->note ?? 'تسجيل تالف'
            ]);
        });

        return back()->with('success', 'تم تسجيل التالف وخصمه من المخزن بنجاح.');
    }
}
