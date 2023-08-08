<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;


class Customer extends Model
{
    use HasApiTokens;

    protected $fillable=[
        'user_name',
        'password',
        'first_name',
        'last_name',
        'email',
        'phone_number'

    ];


    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function customerBillingDetails(): HasMany
    {
        return $this->hasMany(CustomerBillingDetail::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
