<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "paymentMethodCode" => $this->paymentMethod->provider_code,
            "status" => $this->status,
            "subtotal" => $this->subtotal,
            "deliveryFees" => $this->delivery_fee,
            "taxes" => $this->taxes,
            "totalPrice" => $this->total_price,
            "orderDate" => $this->created_at->format('d M, Y'),
            "address" => new AddressResource($this->address),
            "items" => OrderItemResource::collection($this->items)
        ];
    }
}
