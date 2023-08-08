<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginCustomerRequest;
use App\Http\Requests\RegisterCustomerRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return CustomerResource::collection(Customer::paginate());
    }


    /**
     * @param StoreCustomerRequest $storeCustomerRequest
     * @return CustomerResource
     */
    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();

        $hashedPassword = Hash::make(Arr::get($data, 'password'));

        $customer = Customer::create([
            'user_name' => Arr::get($data, 'user_name'),
            'password' => $hashedPassword,
            'first_name' => Arr::get($data, 'first_name'),
            'last_name' => Arr::get($data, 'last_name'),
            'email' => Arr::get($data, 'email'),
        ]);

        return CustomerResource::make($customer);

    }


    /**
     * @param Customer $customer
     * @return CustomerResource
     */
    public function show(Customer $customer)
    {
        return CustomerResource::make($customer);
    }

    /**
     * @param UpdateCustomerRequest $updateCustomerRequest
     * @param Customer $customer
     * @return CustomerResource
     */
    public function update(UpdateCustomerRequest $updateCustomerRequest, Customer $customer)
    {
        $customer->update($updateCustomerRequest->validated());

        return CustomerResource::make($customer);
    }

    /**
     * @param Customer $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json(['message' => 'Customer deleted']);
    }


    public function login(LoginCustomerRequest $request)
    {

        $data = $request->validated();

        $customer = Customer::where('email', Arr::get($data, 'email'))->first();

        if (blank($customer) || !Hash::check(Arr::get($data, 'password'), $customer->password)) {
            return response()->json(['Unauthorized'], 401);
        }

        $token = $customer->createToken('api_token', [], now()->addMinutes(720));

        return response()->json([
            'message' => 'Logged in successfully',
            'access_token' => $token->plainTextToken,
            'customer_id' => $customer
        ], 200);

    }
}
