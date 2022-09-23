<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanPayments extends Model
{
    use HasFactory;
    protected $fillable = ['loan_id', 'payment_due_date', 'due_amount', 'paid_amount', 'status'];

    public function loan(){
        return $this->belongsTo('\App\Models\Loan');
    }

    public static function boot()
    {
        parent::boot();
        // if total installment amount has been paid then update loan status to paid
        static::saved(function ($loanPayment) {
            if ($loanPayment->loan->total_remain === 0) {
                $loanPayment->loan()->where('status', '<>', 'paid')->update(['status' => 'paid']);
                $loanPayment->where('status', '<>', 'paid')->update(['status' => 'paid']);
            }
        });
    }
}
