<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CartItem extends Model
{
    use HasFactory;

      protected $fillable = [
          'customer_id',
          'product_id',
          'quantity',
          'session_id',
      ];

     public function customer(): HasOne
     {
         return $this->hasOne(Customer::class);
     }

      public function product(): HasOne
      {
          return $this->hasOne(Product::class);
      }
}
