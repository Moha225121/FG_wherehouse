<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaleController extends Controller
{
    public function create(Request $request)
    {
        $branchID = Auth::guard('employee')->user()->branchID;
        
        // Items search: no car model search per requirements. 
        // Searching by shelf_number or glass_type only.
        $query = Item::with(['carModel', 'glassPosition'])
            ->where('branchID', $branchID)
            ->where('stock_quantity', '>', 0);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('shelf_number', 'like', "%{$search}%")
                  ->orWhere('glass_type', 'like', "%{$search}%");
            });
        }

        $items = $query->get();
        return view('employee.pos', compact('items'));
    }

    public function store(Request $request)
    {
        // DEBUG: log session + cookie info to help track unexpected logout
        try {
            Log::info('[DEBUG] Employee Sale store start', [
                'session_id' => session()->getId(),
                'session_all' => session()->all(),
                'cookies' => $request->headers->get('cookie'),
                'x_forwarded_proto' => $request->header('x-forwarded-proto'),
            ]);
        } catch (\Throwable $e) {
            // ignore logging errors in production debug helper
        }
        $request->validate([
            'itemID' => 'required|exists:items,id',
            'note' => 'nullable|string'
        ], [],['itemID' => 'الصنف']);

        $employee = Auth::guard('employee')->user();
        $item = Item::where('id', $request->itemID)->where('branchID', $employee->branchID)->firstOrFail();

        if ($item->stock_quantity < 1) {
            return back()->withErrors(['error' => 'الكمية غير متوفرة في المخزن.']);
        }

        DB::transaction(function () use ($request, $employee, $item) {
            $systemPrice = $item->retail_price;
            $soldPrice = $systemPrice;
            
            $discount = 0;
            $overprice = 0;

            if ($soldPrice < $systemPrice) {
                $discount = $systemPrice - $soldPrice;
            } elseif ($soldPrice > $systemPrice) {
                $overprice = $soldPrice - $systemPrice;
            }

            // Record Sale
            Sale::create([
                'branchID' => $employee->branchID,
                'employeeID' => $employee->id,
                'itemID' => $item->id,
                'quantity' => 1,
                'system_price' => $systemPrice,
                'sold_price' => $soldPrice,
                'discount' => $discount,
                'overprice' => $overprice,
                'note' => $request->note,
                'status' => 'completed'
            ]);

            // Deduct Stock
            $item->decrement('stock_quantity', 1);
        });

        try {
            Log::info('[DEBUG] Employee Sale store end', [
                'session_id' => session()->getId(),
                'session_all' => session()->all(),
            ]);
        } catch (\Throwable $e) {}

        return redirect()->back()->with('success', 'تمت عملية البيع بنجاح.');
    }
}