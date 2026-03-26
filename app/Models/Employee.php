<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Employee extends Authenticatable {
    protected $fillable =['branchID', 'name', 'username', 'password', 'status'];
    protected function casts(): array { return['password' => 'hashed']; }
    
    public function branch() { return $this->belongsTo(Branch::class, 'branchID'); }
}