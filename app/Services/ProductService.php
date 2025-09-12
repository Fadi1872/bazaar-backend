<?php

namespace App\Services;

use App\Http\Resources\ProductCardResource;
use App\Models\Product;
use App\Models\ProductCategory;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    protected function runFilterQuery(array $criteria, int $perPage, ?float $lng, ?float $lat)
    {
        $productsQuery = Product::available()
            ->with(['user', 'category', 'image'])
            ->select('products.*')
            ->join('stores', 'products.user_id', '=', 'stores.user_id')
            ->leftJoin('addresses', 'stores.address_id', '=', 'addresses.id');

        if (isset($criteria['name'])) {
            $productsQuery->where('products.name', 'like', "%" . $criteria['name'] . "%");
        }

        if (!empty($criteria['min_rating'])) {
            $productsQuery->where('products.rating', '>=', $criteria['min_rating']);
        }

        if (!empty($criteria['price_min'])) {
            $productsQuery->where('products.price', '>=', $criteria['price_min']);
        }

        if (!empty($criteria['price_max'])) {
            $productsQuery->where('products.price', '<=', $criteria['price_max']);
        }

        if (!empty($criteria['category_ids'])) {
            $productsQuery->whereIn('products.product_category_id', $criteria['category_ids']);
        }

        if (!empty($criteria['cities'])) {
            $productsQuery->whereIn('addresses.city', $criteria['cities']);
        }

        if ($lng && $lat && empty($criteria['cities'])) {
            $productsQuery->selectRaw(
                "ST_Distance_Sphere(point(addresses.longitude, addresses.latitude), point($lng, $lat)) AS distance"
            );
        }

        $sub = DB::table(DB::raw("({$productsQuery->toSql()}) as sub"))
            ->mergeBindings($productsQuery->getQuery())
            ->select('*', DB::raw("ROW_NUMBER() OVER (PARTITION BY user_id ORDER BY rating DESC) as rn"));

        $wrapped = DB::table(DB::raw("({$sub->toSql()}) as ranked"))
            ->mergeBindings($sub)
            ->where('rn', '<=', 3);

        $products = DB::table(DB::raw("({$wrapped->toSql()}) as final"))
            ->mergeBindings($wrapped);

        if ($lng && $lat && empty($criteria['cities'])) {
            $products->orderBy('distance', 'asc');
        } else {
            $products->orderBy('rating', 'desc');
        }

        $products = $products->paginate($perPage);

        $productIds = collect($products->items())->pluck('id');
        $productModels = Product::with(['image', 'category', 'user', 'comments' => function ($query) {
            $userId = Auth::id();
            $query->with(['user', "user.image"])
                ->withCount('likes')
                ->when($userId, function ($query) use ($userId) {
                    $query->withExists([
                        'likes as is_liked' => function ($q) use ($userId) {
                            $q->where('user_id', $userId);
                        }
                    ]);
                })
                ->orderByRaw("FIELD(sentiment, 'positive', 'neutral', 'negative')")
                ->take(2);
        }])
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $products->getCollection()->transform(
            fn($row) => $productModels[$row->id] ?? $row
        );

        return $products;
    }

    /**
     * filter the products
     * 
     * @param arrat $crieteria - min rating, price range, category ids, cities
     * @param int $perPage - pagination page
     * @return LengthAwarePaginator
     */
    public function filter(array $criteria, int $perPage = 15)
    {
        $user      = Auth::user();
        $address   = $user->addresses()->first();
        $hasCoords = $address && isset($address->latitude, $address->longitude);
        $lng       = $hasCoords ? $address->longitude : null;
        $lat       = $hasCoords ? $address->latitude : null;

        $page = request()->get('page', 1);

        if ($page == 1) {
            $cacheKey = 'products_filter:' . Auth::id() . ':' . md5(json_encode($criteria)) . ':page:1';

            return Cache::remember($cacheKey, now()->addMinutes(10), function () use (
                $criteria,
                $perPage,
                $lng,
                $lat
            ) {
                return $this->runFilterQuery($criteria, $perPage, $lng, $lat);
            });
        }

        return $criteria;
    }

    /**
     * create new product
     * 
     * @param array $data - product data
     * @param UploadedFile $image - product image
     * @return Product
     * 
     * @throws Exception
     */
    public function create(array $data, UploadedFile | null $image)
    {
        DB::beginTransaction();
        $storedImagePath = null;

        try {
            $category = ProductCategory::firstOrCreate([
                "name" => $data['product_category']
            ]);
            unset($data['product_category']);
            $data['product_category_id'] = $category->id;

            $data['user_id'] = Auth::id();
            $product = Product::create($data);
            $storage = new ImageStorage();
            if ($image) {
                $storedImagePath = $storage->uploadImage($image, ImageStorage::PRODUCT_IMAGE);
                $product->image()->create([
                    "path" => $storedImagePath
                ]);
            }

            DB::commit();
            return new ProductCardResource($product->load(['image', 'comments' => function ($query) {
                $query->with('user')
                    ->orderByRaw("FIELD(sentiment, 'positive', 'neutral', 'negative')")
                    ->take(2);
            }]));
        } catch (Exception $e) {
            DB::rollBack();
            if ($storedImagePath && Storage::disk('public')->exists($storedImagePath))
                $storage->deleteImage($storedImagePath);

            throw $e;
        }
    }

    /**
     * update the product
     * 
     * @param Product $product
     * @return Product
     * 
     * @throws Exception
     */
    public function update(Product $product, array $data, UploadedFile | null $image = null)
    {
        DB::beginTransaction();
        $storedImagePath = null;
        $storage = new ImageStorage();

        try {
            $product->update($data);

            if ($image) {
                $storedImagePath = $storage->uploadImage($image, ImageStorage::PRODUCT_IMAGE);

                if ($product->image && Storage::disk('public')->exists($product->image->path)) {
                    $storage->deleteImage($product->image->path);
                }

                $product->image()->updateOrCreate([], [
                    "path" => $storedImagePath
                ]);
            }

            DB::commit();
            return $product;
        } catch (Exception $e) {
            DB::rollBack();

            if ($storedImagePath && Storage::disk('public')->exists($storedImagePath)) {
                $storage->deleteImage($storedImagePath);
            }

            throw $e;
        }
    }

    /**
     * Delete a product and its related image
     * 
     * @param Product $product
     * @return bool
     * 
     * @throws Exception
     */
    public function delete(Product $product)
    {
        DB::beginTransaction();

        try {
            $storage = new ImageStorage();

            if ($product->image && Storage::disk('public')->exists($product->image->path)) {
                $storage->deleteImage($product->image->path);
                $product->image()->delete();
            }

            $product->comments()->delete();
            $product->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
