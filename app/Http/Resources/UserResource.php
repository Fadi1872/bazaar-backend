<?php

namespace App\Http\Resources;

use App\Services\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            "age" => $this->age != null ? intval($this->age) : null,
            "phone_number" => $this->number,
            "gender" => $this->gender != null ? intval($this->gender) : null,
            "email" => $this->email,
            "profile_image" => $this->image ? ImageStorage::getUrl($this->image->path) : null
        ];
    }
}
