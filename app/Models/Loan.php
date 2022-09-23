<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;
    protected $fillable = ['customer_id', 'amount', 'term', 'status', 'approved_by_id'];
    protected $appends = ['total_paid', 'total_remain'];

    public function loanPayments()
    {
        return $this->hasMany('App\Models\LoanPayments', 'loan_id');
    }

    public function getTotalPaidAttribute() {
        $totalPaid = $this->loanPayments->where('status','paid')->sum('paid_amount');
        return ($totalPaid) ? round($totalPaid,5) : 0;
    }

    public function getTotalRemainAttribute() {
        $totalRemain = $this->amount - $this->total_paid;
        return ($totalRemain) ? round($totalRemain,5) : 0;
    }

    public function customer(){
        return $this->belongsTo('\App\Models\Customer');
    }
}
