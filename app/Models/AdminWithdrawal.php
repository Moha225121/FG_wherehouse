<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminWithdrawal extends Model {
    protected $table = 'admin_withdrawals';
    protected $fillable = ['adminID', 'amount', 'note', 'withdrawn_at'];

    public function admin() { return $this->belongsTo(Admin::class, 'adminID'); }
}
