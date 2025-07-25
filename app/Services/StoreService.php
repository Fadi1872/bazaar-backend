<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class StoreService
{
    /**
     * Filter the stores based on rating, category and location
     * 
     * @param array $criteria
     * @param int   $perPage  Number of items per page
     * @return LengthAwarePaginator
     */
    public function ShowStoreWithFilters(array $criteria, int $perPage = 15)
    {
        $user       = Auth::user();
        $address    = $user->addresses()->first();
        $hasCoords  = $address && isset($address->latitude, $address->longitude);
        $lng        = $hasCoords ? $address->longitude : null;
        $lat        = $hasCoords ? $address->latitude  : null;

        $query = Store::query()
            ->leftJoin('addresses', 'stores.address_id', '=', 'addresses.id')
            ->select('stores.*');

        if (isset($criteria['rating'])) {
            $query->where('stores.rating', '>=', $criteria['rating']);
        }

        if (! empty($criteria['category_ids'])) {
            $query->whereIn('stores.store_category_id', $criteria['category_ids']);
        }

        if (! empty($criteria['cities'])) {
            $query->whereIn('addresses.city', $criteria['cities']);
        }

        $query->orderBy('stores.rating', 'desc');

        if ($hasCoords && empty($criteria['city'])) {
            $query->selectRaw(
                "ST_Distance_Sphere(
                point(addresses.longitude, addresses.latitude),
                point(?, ?)
            ) AS distance",
                [$lng, $lat]
            )
                ->orderBy('distance', 'asc');
        }

        return $query->paginate($perPage);
    }

    /**
     * create new store
     * 
     * @param array $data
     * @param UploadedFile | null $image
     * 
     * @return Store
     * @throws Throwable
     */
    public function createStore(array $data, UploadedFile | null $image)
    {
        DB::beginTransaction();
        $storedImagePath = null;
        try {
            $data['user_id'] = Auth::id();
            $store = Store::create($data);
            $storage = new ImageStorage();
            $storedImagePath = $storage->uploadImage($image, ImageStorage::STORE_IMAGE);
            $store->image()->create([
                "path" => $storedImagePath
            ]);

            DB::commit();
            return $store;
        } catch (Throwable $e) {
            DB::rollBack();
            if ($storedImagePath && Storage::disk('public')->exists($storedImagePath))
                $storage->deleteImage($storedImagePath);
            throw $e;
        }
    }

    /**
     * update store data
     * 
     * @param Store $store
     * @param array $data
     * @param UploadedFile | null $image
     * 
     * @return Store
     * @throws Throwable
     */
    public function updateStore(Store $store, array $data, UploadedFile | null $image)
    {
        DB::beginTransaction();
        try {
            $store->update([
                'name'              => $data['name'],
                'description'       => $data['description'],
                'store_category_id' => $data['store_category_id'],
                'location_type'     => $data['location_type'],
                'address_id'        => $data['address_id'] ?? null,
            ]);

            if ($image) {
                $storage = new ImageStorage();
                if ($store->image) {
                    Storage::disk('public')->delete($store->image->path);
                    $store->image()->delete();
                }
                $path = $storage->uploadImage($image, ImageStorage::STORE_IMAGE);
                $store->image()->create([
                    'path' => $path,
                ]);
            }

            DB::commit();
            return $store;
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            throw $e;
        }
    }

    /**
     * delete the store
     * 
     * @param Store #store
     */
    public function deleteStore(Store $store)
    {
        DB::beginTransaction();

        try {
            if ($store->image) {
                Storage::disk('public')->delete($store->image->path);
                $store->image()->delete();
            }
            $store->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
