<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\CustomerResource;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class CartItemController extends Controller
{

    public function __contruct()
    {
        $this->authorizeResource(CartItem::class, 'cartItem');
    }
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index($session_id): AnonymousResourceCollection
    {
        $customer_id = auth('customer')->user()->id ?? null;

        if ($customer_id === null) {
            $cartItemsQuery = CartItem::where('session_id', '=', $session_id);
        } else {
            $sessionItemsQuery = CartItem::where('session_id', '=', $session_id)->whereNull('customer_id');

            if ($sessionItemsQuery->exists()) {
                $sessionCartItems = $sessionItemsQuery->get();
                $customerCartItems = CartItem::where('customer_id', '=', $customer_id)->get();

                foreach ($sessionCartItems as $sessionCartItem) {
                    foreach ($customerCartItems as $customerCartItem) {
                        if ($sessionCartItem->product_id === $customerCartItem->product_id) {
                            // Add quantity to the customer's item
                            $customerCartItem->quantity += $sessionCartItem->quantity;
                            $customerCartItem->save();

                            // Delete the session item
                            $sessionCartItem->delete();
                        }
                    }
                }

                $sessionItemsQuery->update(['session_id' => null, 'customer_id' => $customer_id]);
            }

            $cartItemsQuery = CartItem::where('customer_id', '=', $customer_id);
        }

        $cartItems = $cartItemsQuery->get();

        return CartItemResource::collection($cartItems);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCartItemRequest $storeCartItemRequest
     * @return CartItemResource
     */

    public function store(StoreCartItemRequest $storeCartItemRequest)
    {
        $sessionId = $storeCartItemRequest->header('X-Session');
        $customer_id = auth('customer')->user()->id ?? null;
        $data = $storeCartItemRequest->validated();

        if ($customer_id !== null) {

            $cartItem = CartItem::firstOrNew([
                'customer_id' => $customer_id,
                'product_id' => Arr::get($data, 'product_id'),

            ]);

        } else {
            $cartItem = CartItem::firstOrNew([
                'session_id' => $sessionId,
                'product_id' => Arr::get($data, 'product_id'),

            ]);

        }

        if ($cartItem->exists) {

            $existingQuantity = 0;
            $inputQuantity = Arr::get($data, 'quantity');

            if (!(Arr::get($data, 'fromCart') ?? false)) {
                $existingQuantity = $cartItem->quantity;


            }
            $cartItem->quantity = $inputQuantity + $existingQuantity;
        } else {
            $cartItem->fill([
                'customer_id' => $customer_id,
                'quantity' => Arr::get($data, 'quantity')
            ]);
        }

        $cartItem->save();

        return CartItemResource::make($cartItem);
    }

    /**
     * Display the specified resource.
     *
     * @param $cart_item
     * @return AnonymousResourceCollection
     */

    /**
     * @param $session_id
     * @param CartItem $cartItem
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($session_id, CartItem $cartItem): \Illuminate\Http\JsonResponse
    {

        if ($this->authorize('delete', [$cartItem, $session_id])) {
            $cartItem->delete();
            return response()->json(['message' => 'Cart Item deleted']);
        } else {
            return response()->json(['error' => 'Unauthorized'], 403); // 403 Forbidden
        }
    }
}
