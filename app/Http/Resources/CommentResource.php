<?php

namespace App\Http\Resources;

use App\Services\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            "id" => $this->id,
            "comment" => $this->body,
            "rating" => intval($this->rating),
            "isLiked" => $this->resource->offsetExists('is_liked') ? $this->is_liked : false,
            "likes" => $this->resource->offsetExists('likes_count') ? $this->likes_count : 0,
            "profilePhoto" => $this->user->image ? ImageStorage::getUrl($this->user->image->path) : null,
            "name" => $this->user->name,
        ];
        return $data;
    }
}
