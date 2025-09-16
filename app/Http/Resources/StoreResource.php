<?php

namespace App\Http\Resources;

use App\Services\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class StoreResource extends JsonResource
{
    protected $productCat;
    protected $products;

    public function __construct($resource, $bhbh = null, array $extra = [])
    {
        parent::__construct($resource);
        $this->productCat = $extra['categories'] ?? [];
        $this->products   = $extra['products'] ?? [];
    }

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
            "sort" => $this->whenLoaded('category', fn() => $this->category->name, null),
            "address" => $this->address->city,
            "latitude" => $this->address->latitude,
            "longitude" => $this->address->longitude,
            "storeNumber" => $this->address->phone_number,
            "image" => $this->image ? ImageStorage::getUrl($this->image->path) : null,
            "reviews" => $this->relationLoaded('comments')
                ? CommentResource::collection($this->comments)
                : [],
            "categories" => $this->productCat ?? [],
            "products" => $this->products ?? [],
            "isFavorite" => $this->isFavoritedBy(Auth::id())
        ];
    }
}
