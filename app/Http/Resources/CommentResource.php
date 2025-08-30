<?php

namespace App\Http\Resources;

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
            "body" => $this->body,
            "rating" => $this->rating
        ];

        if ($this->resource->offsetExists('is_liked'))
            $data["is_liked"] = $this->is_liked;
        if ($this->resource->offsetExists('likes_count'))
            $data["likes_count"] = $this->likes_count;

        $data["user"] = new UserResource($this->user);

        return $data;
    }
}
