<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone_number' => ['required', 'numeric'],
            'total_amount' => ['required', 'numeric', 'gte:8'],
            'card_holder' => ['required', 'string'],
            'card_number' => ['required', 'numeric', 'digits:16'],
            'card_exp_month' => ['required', 'numeric'],
            'card_exp_year' => ['required', 'numeric'],
            'card_security_code' => ['required', 'numeric', 'digits:3'],
            'customer_id' => ['nullable'],
             'session_id' => ['nullable'],

        ];
    }
}
