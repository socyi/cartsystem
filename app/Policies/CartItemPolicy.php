<?php

namespace App\Policies;

use App\Models\CartItem;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CartItemPolicy
{
    use HandlesAuthorization;



    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CartItem  $cartItem
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(?Customer $customer = null, CartItem $cartItem, ?string $sessionId = null)
    {
        if ($cartItem->session_id === $sessionId || $cartItem->customer_id === $customer->id) {
            return true;
        }

        return false;
    }


}
