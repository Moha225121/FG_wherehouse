<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = ['employeeID', 'amount', 'date', 'note'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employeeID');
    }
}
