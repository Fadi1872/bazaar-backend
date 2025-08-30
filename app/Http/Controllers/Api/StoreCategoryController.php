<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchStoreCategoryRequest;
use App\Http\Requests\StoreStoreCategoryRequest;
use App\Http\Resources\StoreCategoryResource;
use App\Models\StoreCategory;
use App\Services\CategoryService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StoreCategoryController extends Controller
{
    use AuthorizesRequests;

    protected CategoryService $service;
    public function __construct()
    {
        $this->service = new CategoryService(StoreCategory::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(SearchStoreCategoryRequest $request)
    {
        try {
            $categories = $this->service->list($request->validated());
            return $this->successResponse("three categories listed.", StoreCategoryResource::collection($categories));
        } catch (Exception $e) {
            return $this->errorResponse("failed to list categories", 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStoreCategoryRequest $request)
    {
        try {
            $category = $this->service->create($request->validated()['name']);
            return $this->successResponse("category created.", new StoreCategoryResource($category));
        } catch (Exception $e) {
            return $this->errorResponse("failed to create category", 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StoreCategory $storeCategory)
    {
        $this->authorize('delete', $storeCategory);

        try {
            if (!$storeCategory->stores()->exists()) {
                $this->service->delete($storeCategory);
                return $this->successResponse("category deleted successfully.");
            } else return $this->errorResponse("this category is associated with a product", 500);
        } catch (Exception $e) {
            return $this->errorResponse("failed to delete category.", 500);
        }
    }
}
