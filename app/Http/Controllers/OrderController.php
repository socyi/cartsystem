<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\OrderResource;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return OrderResource::collection(Order::paginate());
    }

    /**
     * @param StoreOrderRequest $storeOrderRequest
     * @return OrderResource
     */
    public function store(StoreOrderRequest $storeOrderRequest)
    {



        return OrderResource::make(Order::create($storeOrderRequest->validated()));
    }

    /**
     * @param Order $order
     * @return OrderResource
     */
    public function show(Order $order)
    {
        return OrderResource::make($order);
    }

    /**
     * @param UpdateOrderRequest $updateOrderRequest
     * @param Order $order
     * @return OrderResource
     */
    public function update(UpdateOrderRequest $updateOrderRequest, Order $order)
    {
        $order->update($updateOrderRequest->validated());

        return OrderResource::make($order);
    }
    /**
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json(['message' => 'Order deleted']);

    }

    public function purchase(StoreOrderRequest $request){

        //1. validation
        $data = $request->validated();
        //2. get access token
        $tokenBody = ["grant_type" => "password",
            "client_id" => 67,
            "client_secret" => "4Lnkhif652blnI1RMnMlmtcjhGt4h5KdOixH0GlR",
            "username" => "test_gwtest",
            "password" => "pTcMclRKCOAl"];


        $requestURL = 'https://sandbox.gateway-services.com/api/oauth/token';
        $response = Http::asJson()->acceptJson()
            ->timeout(60)->post($requestURL, $tokenBody)
            ->onError(function (Response $response) {
                $responseRaw = $response->body();
                $responseCode = $response->status();
                $responseDetails = 'Error';
                $transactionStatus = 'PAYMENT_STATUS_DECLINED';
            });

        if (!$response->successful()) {
            $errorMessage = 'Error occurred while trying to get access token from the API.';
            return response()->json(['error' => $errorMessage], 400);
        }

        $tokenResponseData = $response->json();
        $token = Arr::get($tokenResponseData, 'access_token');


        //3. create order
        $order = Order::create([
           'email' => Arr::get($data, 'email'),
            'total_amount' => Arr::get($data, 'total_amount'),
        ]);

        $orderId ='SOC9999'.$order->id;

        //4 Call Purchase API
        $bodyPayment = [
            "order_id" => $orderId,
            "ip_address" => "154.233.142.119",
            "amount" => Arr::get($data, 'total_amount') *100,
            "currency" => "USD",
            "description" => "Payment of order 001",
            "customer_first_name" => Arr::get($data, 'first_name'),
            "customer_last_name" => Arr::get($data, 'last_name'),
            "customer_phone_code" => "0044",
            "customer_phone" => Arr::get($data, 'phone_number'),
            "customer_email" => Arr::get($data, 'email'),
            "card_holder" => Arr::get($data, 'card_holder'),
            "card_number" => Arr::get($data, 'card_number'),
            "card_exp_month" => Arr::get($data, 'card_exp_month'),
            "card_exp_year" =>  Arr::get($data, 'card_exp_year'),
            "card_security_code" => Arr::get($data, 'card_security_code'),
            "return_url" => "https:\/\/localhost\/return"
        ];

        $requestPaymentURL = 'https://sandbox.gateway-services.com/api/purchase';



        $errorDetails = [];
        $responsePayment = Http::asJson()->acceptJson()
            ->withToken($token)
            ->timeout(300)->post($requestPaymentURL, $bodyPayment)
            ->onError(function (\Illuminate\Http\Client\Response $error) use (&$errorDetails){
                $errorDetails['status'] = $error->status();
                $errorDetails['message'] = $error->json()['message'] ?? 'Unknown Error';
                logger($error);
            });

        if ($responsePayment->successful() === false) {

            return response()->json([
                'error' => [
                    'message' => $errorDetails['message'],
                ]
            ], $errorDetails['status']);
        }



        $paymentResponseData = $responsePayment->json();


        //5 Update order

        $status = Arr::get($paymentResponseData,'status');
        $status_description = Arr::get($paymentResponseData,'status_description');


        $order->fill([
            'status' => $status,
            'status_description' => $status_description,
        ]);

        $order->save();

        //6 Order items and cart item


        $customerID = $request->customer_id;
        $sessionID= $request->session_id;

        if ($customerID !== -1) {
            $cartItems = CartItem::where('customer_id', $customerID)->get();

        } else {
            $cartItems = CartItem::where('session_id', $sessionID)->get();
        }


        $orderItems = [];
        foreach ($cartItems as $cartItem) {
            $orderItems[] = [
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
            ];
        }

        OrderItem::insert($orderItems);

        $cartItems->each->delete();



        return response()->json(['message' => 'Purchase completed successfully for order '.$orderId], 200);

    }
}
