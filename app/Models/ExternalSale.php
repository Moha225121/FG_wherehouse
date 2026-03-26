<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class ExternalSale extends Model {
    protected $fillable =['branchID', 'employeeID', 'sale_type', 'amount'];
    public function branch() { return $this->belongsTo(Branch::class, 'branchID'); }
    public function employee() { return $this->belongsTo(Employee::class, 'employeeID'); }
}