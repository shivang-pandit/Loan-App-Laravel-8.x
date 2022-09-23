<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticable
{
    use HasFactory, HasApiTokens;
    protected $guard = 'customer';
    protected $hidden = ['password'];

    public function loans()
    {
        return $this->hasMany('App\Models\Loan', 'customer_id');
    }
}
