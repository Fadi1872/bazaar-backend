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
            "image" => $this->image ? ImageStorage::getUrl($this->image->path) : null,
            "status" => $this->created_at->lt(now()->subWeek()) ? null: "new",
            "price" => $this->price,
            "name" => $this->name,
            "store_id" => $this->store->id,
            "markerName" => $this->store->name,
            "category" => $this->category->name,
            "rating" => $this->rating,
            "comments" => $this->comments ? CommentResource::collection($this->comments) : null
        ];
    }
}
