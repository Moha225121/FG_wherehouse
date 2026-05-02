<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Item;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminSaleController extends Controller
{
    public function index()
    {
        // View all sales globally, newest first
        $sales = Sale::with(['branch', 'employee', 'admin', 'item.carModel', 'item.glassPosition'])
                     ->orderBy('id', 'desc')
                     ->paginate(50);

        return view('admin.sales.index', compact('sales'));
    }

    public function create(Request $request)
    {
        $branches = Branch::orderBy('name')->get();

        $query = Item::with(['branch', 'carModel', 'glassPosition'])
            ->where('stock_quantity', '>', 0)
            ->orderBy('id', 'desc');

        if ($request->filled('branchID')) {
            $query->where('branchID', $request->branchID);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('shelf_number', 'like', "%{$search}%")
                    ->orWhere('glass_type', 'like', "%{$search}%");
            });
        }

        $items = $query->get();

        return view('admin.sales.create', compact('items', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'itemID' => 'required|exists:items,id',
            'sold_price' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
        ], [], [
            'itemID' => 'الصنف',
            'sold_price' => 'سعر البيع',
        ]);

        $item = Item::findOrFail($request->itemID);

        if ($item->stock_quantity < 1) {
            return back()->withErrors(['error' => 'الكمية غير متوفرة في المخزن.']);
        }

        DB::transaction(function () use ($request, $item) {
            $systemPrice = $item->retail_price;
            $soldPrice = $request->filled('sold_price') ? (float) $request->sold_price : (float) $systemPrice;

            $discount = 0;
            $overprice = 0;

            if ($soldPrice < $systemPrice) {
                $discount = $systemPrice - $soldPrice;
            } elseif ($soldPrice > $systemPrice) {
                $overprice = $soldPrice - $systemPrice;
            }

            Sale::create([
                'branchID' => $item->branchID,
                'employeeID' => null,
                'adminID' => Auth::guard('admin')->id(),
                'itemID' => $item->id,
                'quantity' => 1,
                'system_price' => $systemPrice,
                'sold_price' => $soldPrice,
                'discount' => $discount,
                'overprice' => $overprice,
                'note' => $request->note,
                'status' => 'completed',
            ]);

            $item->decrement('stock_quantity', 1);
        });

        return redirect()->route('admin.pos')->with('success', 'تمت عملية البيع بواسطة الإدارة بنجاح.');
    }

    public function undoSale(Sale $sale)
    {
        if ($sale->status === 'refunded') {
            return back()->withErrors(['error' => 'تم التراجع عن هذه العملية مسبقاً.']);
        }

        DB::transaction(function () use ($sale) {
            // 1. Mark as refunded
            $sale->update(['status' => 'refunded']);

            // 2. Return item to stock
            $sale->item()->increment('stock_quantity', $sale->quantity);
        });

        return redirect()->back()->with('success', 'تم التراجع عن البيع وإرجاع الصنف للمخزن بنجاح.');
    }
}