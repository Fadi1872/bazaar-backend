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
            "rating" => $this->rating
        ];

        if ($this->resource->offsetExists('is_liked'))
            $data["isLiked"] = $this->is_liked;
        if ($this->resource->offsetExists('likes_count'))
            $data["likes"] = intval($this->likes_count);

        $data["profilePhoto"] = $this->user->image ? ImageStorage::getUrl($this->user->image->path) : null;
        $data["name"] = $this->user->name;

        return $data;
    }
}
