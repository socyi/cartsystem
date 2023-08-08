<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\CustomerResource;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class CartItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($session_id)
    {
        $customer_id = auth('customer')->user()->id ?? null;

        if ($customer_id === null) {
            $cartItemsQuery = CartItem::where('session_id', '=', $session_id);
        } else {
            $sessionItemsQuery = CartItem::where('session_id', '=', $session_id);
            if ($sessionItemsQuery->exists()) {
                // Use update() to execute the update query
                $sessionItemsQuery->update(['session_id' => null, 'customer_id' => $customer_id]);
            }

            $cartItemsQuery = CartItem::where('customer_id', '=', $customer_id);
        }

        // Use get() to execute the query and retrieve the cart items
        $cartItems = $cartItemsQuery->get();

        return CartItemResource::collection($cartItems);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
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
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function show($session_id)
    {
//        $cartItem = CartItem::where('session_id', '=',$session_id);
//        return CartItemResource::collection($cartItem->get());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update($session_id, Product $product_id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * //     */
//    public function destroy($session_id,  $product_id)
//    {
//
//        dd($product_id);
////fedros
////        $product->cartItems()->where('session_id', '=',$session_id)->firstOrFail()->delete();
//        $cartItem = CartItem::where('session_id', '=', $session_id)
//            ->where('product_id','=',$product_id)
//            ->first();
//
//
//        $cartItem->delete();
//        return response()->json(['message' => 'Cart Item deleted']);
//
//    }

    public function destroy($session_id, Product $product)
    {

        $customer_id = auth('customer')->user()->id ?? null;

        if ($customer_id !== null) {

            $product->cartItems()->where('customer_id', '=', $customer_id)->firstOrFail()->delete();

        } else {

            $product->cartItems()->where('session_id', '=', $session_id)->firstOrFail()->delete();
        }

        return response()->json(['message' => 'Cart Item deleted']);

    }
}
