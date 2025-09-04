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
            "rating" => $this->rating,
            "sort" => $this->category->name,
            "address" => $this->address->city,
            "latitude" => $this->address->latitude,
            "longitude" => $this->address->longitude,
            "storeNumber" => $this->address->phone_number,
            "image" => $this->image ? ImageStorage::getUrl($this->image->path) : null,
            'reviews' => CommentResource::collection($this->comments ?? collect()),
        ];
    }
}
