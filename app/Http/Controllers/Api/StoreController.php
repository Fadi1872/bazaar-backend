<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\StoreFilterRequest;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\ProductCardResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StoreResource;
use App\Models\Product;
use App\Models\Store;
use App\Services\CommentService;
use App\Services\StoreService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StoreController extends Controller
{
    use AuthorizesRequests;
    protected StoreService $service;
    protected CommentService $commentService;

    public function __construct(StoreService $service, CommentService $commentService)
    {
        $this->service = $service;
        $this->commentService = $commentService;
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
        $this->authorize('view', Store::class);

        try {
            $store->load([
                'address',
                'image',
                'category',
                'comments' => function ($query) {
                    $query->with('user')
                        ->orderByRaw("FIELD(sentiment, 'positive', 'neutral', 'negative')")
                        ->take(2);
                }
            ]);

            $products = Product::with(['image', 'store'])
                ->whereHas('user', fn($q) => $q->where('user_id', $store->id))
                ->get();


            $categories = $products
                ->groupBy('product_category_id')
                ->map(function ($products) {
                    $category = $products->first()->category;
                    return [
                        'id'       => $category->id,
                        'name'     => $category->name,
                        'products' => ProductCardResource::collection($products->take(2)),
                    ];
                });

            return $this->successResponse("store detailes", [
                "store" => new StoreResource($store),
                "categories" => $categories
            ]);
        } catch (Exception $e) {
            return $this->errorResponse("failed to show store", 500);
        }
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
            $this->service->deleteStore($store);
            return $this->successResponse("store deleted");
        } catch (Exception $e) {
            return $this->errorResponse("failed to delete store", 500);
        }
    }

    /**
     * add a comment to the store
     */
    public function addComment(StoreCommentRequest $request, Store $store)
    {
        try {
            $comment = $this->commentService->create($store, $request->validated());
            return $this->successResponse("comment added", new CommentResource($comment->load('user')));
        } catch (Exception $e) {
            return $this->errorResponse("failed to add comment", 500);
        }
    }

    /**
     * return all store comments
     */
    public function comments(Store $store)
    {
        $this->authorize('viewComments', Store::class);

        try {
            $comments = $this->commentService->all($store);
            return $this->successResponse("comments listed", CommentResource::collection($comments));
        } catch (Exception $e) {
            return $this->errorResponse("failed to list comments", 500);
        }
    }
}
