<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerBillingDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'customer_id' => $this->customer_id,
            'country' => $this->country,
            'city' => $this->city,
            'address' => $this->address,
            'postal_code' => $this->postal_code
        ];
    }
}
