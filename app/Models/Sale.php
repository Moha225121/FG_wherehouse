<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model {
    protected $fillable =[
        'branchID', 'employeeID', 'adminID', 'itemID', 'quantity', 
        'system_price', 'sold_price', 'discount', 'overprice', 'note', 'status'
    ];
    public function branch() { return $this->belongsTo(Branch::class, 'branchID'); }
    public function employee() { return $this->belongsTo(Employee::class, 'employeeID'); }
    public function admin() { return $this->belongsTo(Admin::class, 'adminID'); }
    public function item() { return $this->belongsTo(Item::class, 'itemID'); }
}

