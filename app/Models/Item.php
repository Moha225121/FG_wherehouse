<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Item extends Model {
    protected $fillable =[
        'branchID', 'carModelID', 'glassPositionID', 'glass_type', 
        'shelf_number', 'wholesale_price', 'retail_price', 'stock_quantity', 'damaged_quantity'
    ];

    public function branch() { return $this->belongsTo(Branch::class, 'branchID'); }
    public function carModel() { return $this->belongsTo(CarModel::class, 'carModelID'); }
    public function glassPosition() { return $this->belongsTo(GlassPosition::class, 'glassPositionID'); }
}