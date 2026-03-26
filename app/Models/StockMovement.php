<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model {
    protected $fillable = ['itemID', 'employeeID', 'adminID', 'movement_type', 'quantity', 'note'];
    public function item() { return $this->belongsTo(Item::class, 'itemID'); }
    public function employee() { return $this->belongsTo(Employee::class, 'employeeID'); }
    public function admin() { return $this->belongsTo(Admin::class, 'adminID'); }
}

