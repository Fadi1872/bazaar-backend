<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchProductRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\ProductCardResource;
use App\Models\Product;
use App\Services\CommentService;
use App\Services\ProductService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    use AuthorizesRequests;
    protected ProductService $service;
    protected CommentService $commentService;

    public function __construct(ProductService $service, CommentService $commentService)
    {
        $this->service = $service;
        $this->commentService = $commentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(SearchProductRequest $request)
    {
        try {
            $products = $this->service->filter($request->validated());
            return $this->successResponse("all products listed", ProductCardResource::collection($products));
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $image = $request->file("image") ?? null;
            $data = $request->validated();
            unset($data['image']);
            $product = $this->service->create($data, $image);
            return $this->successResponse("product created successfully.", $product);
        } catch (Exception $e) {
            return $this->errorResponse("failed to create product.", 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $this->authorize('view', $product);
        try {
            $userId = Auth::id();
            $product = $product->load([
                'image',
                'category',
                'comments' => function ($query) use ($userId) {
                    $query->with('user', 'user.image')
                        ->withCount('likes')
                        ->when($userId, function ($q) use ($userId) {
                            $q->withExists([
                                'likes as is_liked' => function ($sub) use ($userId) {
                                    $sub->where('user_id', $userId);
                                }
                            ]);
                        })
                        ->orderByRaw("FIELD(sentiment, 'positive', 'neutral', 'negative')")
                        ->take(2);
                }
            ]);
            return $this->successResponse("product detailes", new ProductCardResource($product));
        } catch (Exception $e) {
            return $this->errorResponse("failed to show product", 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            $image = $request->file("image") ?? null;
            $data = $request->validated();
            unset($data['image']);
            $product = $this->service->update($product, $data, $image);
            return $this->successResponse("store updated", $product);
        } catch (Exception $e) {
            return $this->errorResponse("failed to update store", 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        try {
            $store = $this->service->delete($product);
            return $this->successResponse("store deleted");
        } catch (Exception $e) {
            return $this->errorResponse("failed to delete store", 500);
        }
    }

    /**
     * add a comment to the store
     */
    public function addComment(StoreCommentRequest $request, Product $product)
    {
        try {
            $comment = $this->commentService->create($product, $request->validated());
            return $this->successResponse("comment added", new CommentResource($comment->load('user')));
        } catch (Exception $e) {
            return $this->errorResponse("failed to add comment", 500);
        }
    }

    /**
     * return all store comments
     */
    public function comments(Product $product)
    {
        $this->authorize('viewComments', Product::class);

        try {
            $comments = $this->commentService->all($product);
            return $this->successResponse("comments listed", CommentResource::collection($comments));
        } catch (Exception $e) {
            return $this->errorResponse("failed to list comments", 500);
        }
    }
}
