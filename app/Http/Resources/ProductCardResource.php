<?php

namespace App\Http\Resources;

use App\Services\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCardResource extends JsonResource
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
            "price" => $this->price,
            "created_at" => $this->created_at,
            "image_url" => $this->image ? ImageStorage::getUrl($this->image->path) : null,
            "store_id" => $this->store->id,
            "store_name" => $this->store->name
        ];
    }
}
