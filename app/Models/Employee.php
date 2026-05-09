<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Employee extends Authenticatable {
    protected $fillable =[
        'branchID', 'name', 'username', 'password', 'status',
        'salary', 'daily_withdrawal_limit', 'remaining_salary', 'salary_reset_day', 'last_reset_month'
    ];
    protected function casts(): array { return['password' => 'hashed']; }
    
    public function branch() { return $this->belongsTo(Branch::class, 'branchID'); }

    public function withdrawals() { return $this->hasMany(Withdrawal::class, 'employeeID'); }
}