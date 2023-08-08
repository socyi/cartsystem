<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price'
        ];

    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }

    public function product(): HasOne
    {
        return $this->hasOne(Product::class);
    }



}
