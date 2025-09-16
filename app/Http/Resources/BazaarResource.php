<?php

namespace App\Http\Resources;

use App\Services\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class BazaarResource extends JsonResource
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
        $status = $this->getStatus();
    
        return [
            "id" => $this->id,
            "image" => $this->image ? ImageStorage::getUrl($this->image->path) : null,
            "name" => $this->name,
            "details" => $this->description,
            "firstDate" => $this->start_date ? $this->start_date->format('d M, Y') : null,
            "lastDate" => $this->end_date ? $this->end_date->format('d M, Y') : null,
            "status" => $status,
            "address" => $this->address->city,
            "categories" => $this->productCat ?? [],
            "products" => $status === 'upcoming' ? [] : ($this->products ?? []),
            "reviews" => $this->relationLoaded('comments')
                ? CommentResource::collection($this->comments)
                : [],
            "isFavorite" => $this->isFavoritedBy(Auth::id())
        ];
    }

    /**
     * Compute bazaar status
     */
    protected function getStatus(): string
    {
        $now = now();

        if ($this->start_date && $this->start_date->isFuture()) {
            return "upcoming";
        }

        if (
            $this->start_date && $this->end_date &&
            $this->start_date->isPast() && $this->end_date->isFuture()
        ) {
            return "ongoing";
        }

        if ($this->end_date && $this->end_date->isPast()) {
            return "past";
        }

        return "unknown";
    }
}
