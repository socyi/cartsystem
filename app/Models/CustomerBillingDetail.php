<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerBillingDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'country',
        'city',
        'address',
        'postal_code'
    ];

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

}
