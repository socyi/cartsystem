<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules():array
    {
        return [
            'user_name'=>['required', 'unique:'.Customer::class.',user_name'],
            'password' => ['required','string'],
            'first_name' => ['required','string'],
            'last_name' => ['required','string'],
            'email' => ['required','email', 'unique:'.Customer::class .',email'],
            'phone_number' => ['required', 'unique:'.Customer::class .',phone_number']


        ];
    }
}
