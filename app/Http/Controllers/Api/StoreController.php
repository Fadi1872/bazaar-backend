<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFilterRequest;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use App\Services\StoreService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StoreController extends Controller
{
    use AuthorizesRequests;
    protected StoreService $service;
    public function __construct(StoreService $service)
    {
        $this->service = $service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(StoreFilterRequest $request)
    {
        try {
            $stores = $this->service->ShowStoreWithFilters($request->validated());
            return $this->successResponse("all stores listed", StoreResource::collection($stores->load('address', 'image', 'category')));
        } catch (Exception $e) {
            return $this->errorResponse("failed to list stores", 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStoreRequest $request)
    {
        try {
            $image = $request->file("image") ?? null;
            $data = $request->validated();
            unset($data['image']);
            $store = $this->service->createStore($data, $image);
            return $this->successResponse("store created", new StoreResource($store->load('address', 'image', 'category')));
        } catch (Exception $e) {
            return $this->errorResponse("failed to create store", 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        //! this should be added after the products table is added
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStoreRequest $request, Store $store)
    {
        try {
            $image = $request->file("image") ?? null;
            $data = $request->validated();
            unset($data['image']);
            $store = $this->service->updateStore($store, $data, $image);
            return $this->successResponse("store updated", new StoreResource($store->load('address', 'image', 'category')));
        } catch (Exception $e) {
            return $this->errorResponse("failed to update store", 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        $this->authorize('delete', $store);
        try {
            $store = $this->service->deleteStore($store);
            return $this->successResponse("store deleted", $store);
        } catch (Exception $e) {
            return $this->errorResponse("failed to delete store", 500);
        }
    }
}
