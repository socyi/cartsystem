<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
    public function rules(): array
    {
            return [
            'name'=> ['nullable', 'unique:'.User::class.',name'],
            'password' => ['nullable','string'],
            'email' => ['nullable', 'unique:'.User::class .',email']
        ];
    }
}
