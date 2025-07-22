<?php

namespace App\Http\Resources;

use App\Services\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
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
            "name" => $this->name,
            "description" => $this->description,
            "rating" => $this->rating,
            "category" => new StoreCategoryResource($this->category),
            "location_type" => $this->location_type,
            "address" => $this->address ? new AddressResource($this->address) : null,
            "image" => $this->image ? ImageStorage::getUrl($this->image->path) : null
        ];
    }
}
