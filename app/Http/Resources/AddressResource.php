<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"        => $this->id,
            "label"     => $this->label,
            "address"   => $this->city,
            "latitude"  => $this->latitude,
            "longitude" => $this->longitude,
            "phone_number" => $this->phone_number
        ];
    }
}
