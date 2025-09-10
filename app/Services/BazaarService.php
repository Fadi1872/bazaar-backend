<?php

namespace App\Services;

use App\Http\Resources\BazaarResource;
use App\Models\Bazaar;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BazaarService
{
    protected function runFilterQuery(array $criteria, int $perPage, ?float $lng, ?float $lat)
    {
        $bazaarsQuery = Bazaar::with(['user', 'category', 'image', 'address'])
            ->select('bazaars.*')
            ->leftJoin('addresses', 'bazaars.address_id', '=', 'addresses.id');

        if (isset($criteria['name'])) {
            $bazaarsQuery->where('bazaars.name', 'like', "%" . $criteria['name'] . "%");
        }

        if (!empty($criteria['status'])) {
            $now = now();
            switch ($criteria['status']) {
                case 'upcoming':
                    $bazaarsQuery->where('bazaars.start_date', '>', $now);
                    break;
                case 'ongoing':
                    $bazaarsQuery->where('bazaars.start_date', '<=', $now)
                        ->where('bazaars.end_date', '>=', $now);
                    break;
                case 'past':
                    $bazaarsQuery->where('bazaars.end_date', '<', $now);
                    break;
            }
        }

        if (!empty($criteria['start_date']) && !empty($criteria['end_date'])) {
            $bazaarsQuery->where(function ($query) use ($criteria) {
                $query->where('bazaars.start_date', '<=', $criteria['end_date'])
                    ->where('bazaars.end_date', '>=', $criteria['start_date']);
            });
        } elseif (!empty($criteria['start_date'])) {
            $bazaarsQuery->where('bazaars.start_date', '>=', $criteria['start_date']);
        } elseif (!empty($criteria['end_date'])) {
            $bazaarsQuery->where('bazaars.start_date', '<=', $criteria['end_date']);
        }

        if (!empty($criteria['category_ids'])) {
            $bazaarsQuery->whereIn('bazaars.category_id', $criteria['category_ids']);
        }

        if (!empty($criteria['cities'])) {
            $bazaarsQuery->whereIn('addresses.city', $criteria['cities']);
        }

        if ($lng && $lat && empty($criteria['cities'])) {
            $bazaarsQuery->selectRaw(
                "ST_Distance_Sphere(point(addresses.longitude, addresses.latitude), point($lng, $lat)) AS distance"
            );
        }

        if ($lng && $lat && empty($criteria['cities'])) {
            $bazaarsQuery->orderBy('distance', 'asc')
                ->orderBy('bazaars.positiveness', 'desc');
        } else {
            $bazaarsQuery->orderBy('bazaars.positiveness', 'desc')
                ->orderBy('bazaars.start_date', 'asc');
        }

        return $bazaarsQuery->paginate($perPage);
    }

    /**
     * filter the bazaars
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
            $cacheKey = 'bazaars_filter:' . Auth::id() . ':' . md5(json_encode($criteria)) . ':page:1';

            return Cache::remember($cacheKey, now()->addMinutes(10), function () use (
                $criteria,
                $perPage,
                $lng,
                $lat
            ) {
                return $this->runFilterQuery($criteria, $perPage, $lng, $lat);
            });
        }

        return $this->runFilterQuery($criteria, $perPage, $lng, $lat);
    }

    /**
     * Get all products of a specific category in a given bazaar
     *
     * @param Bazaar $bazaar
     * @param int $categoryId
     * @return Collection
     */
    public function getCategoryProducts(Bazaar $bazaar, int $categoryId)
    {
        return $bazaar->products()
            ->where('product_category_id', $categoryId)
            ->with(['image', 'store'])
            ->get();
    }

    /**
     * create new bazaar
     */
    public function create(array $data, UploadedFile | null $image)
    {
        DB::beginTransaction();
        $storedImagePath = null;

        try {
            $data['user_id'] = Auth::id();

            $bazaar = Bazaar::create($data);
            $storage = new ImageStorage();

            if ($image) {
                $storedImagePath = $storage->uploadImage($image, ImageStorage::BAZAAR_IMAGE);
                $bazaar->image()->create([
                    "path" => $storedImagePath
                ]);
            }

            DB::commit();

            return new BazaarResource($bazaar->load(['image', 'category', 'address', 'user', 'comments']));
        } catch (Exception $e) {
            DB::rollBack();
            if ($storedImagePath && Storage::disk('public')->exists($storedImagePath)) {
                $storage->deleteImage($storedImagePath);
            }
            throw $e;
        }
    }

    /**
     * update the bazaar
     */
    public function update(Bazaar $bazaar, array $data, UploadedFile | null $image = null)
    {
        DB::beginTransaction();
        $storedImagePath = null;
        $storage = new ImageStorage();

        try {
            $bazaar->update($data);

            if ($image) {

                if ($bazaar->image && Storage::disk('public')->exists($bazaar->image->path)) {
                    $storage->deleteImage($bazaar->image->path);
                    $bazaar->image()->delete();
                }
                $storedImagePath = $storage->uploadImage($image, ImageStorage::BAZAAR_IMAGE);

                $bazaar->image()->create([
                    "path" => $storedImagePath
                ]);
            }

            DB::commit();
            return $bazaar->refresh();
        } catch (Exception $e) {
            DB::rollBack();
            if ($storedImagePath && Storage::disk('public')->exists($storedImagePath)) {
                $storage->deleteImage($storedImagePath);
            }
            throw $e;
        }
    }

    /**
     * delete a bazaar
     */
    public function delete(Bazaar $bazaar)
    {
        DB::beginTransaction();

        try {
            $storage = new ImageStorage();

            if ($bazaar->image && Storage::disk('public')->exists($bazaar->image->path)) {
                $storage->deleteImage($bazaar->image->path);
                $bazaar->image()->delete();
            }

            $bazaar->comments()->delete();
            $bazaar->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
