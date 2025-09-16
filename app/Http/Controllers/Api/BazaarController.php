<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetCategoryIdRequest;
use App\Http\Requests\SearchBazaarRequest;
use App\Http\Requests\StoreBazaarRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateBazaarRequesst;
use App\Http\Resources\BazaarResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\ProductCardResource;
use App\Models\Bazaar;
use App\Models\Store;
use App\Services\BazaarService;
use App\Services\CommentService;
use App\Services\FavoriteService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BazaarController extends Controller
{
    use AuthorizesRequests;

    protected BazaarService $service;
    protected CommentService $commentService;

    public function __construct(BazaarService $service, CommentService $commentService)
    {
        $this->service = $service;
        $this->commentService = $commentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(SearchBazaarRequest $request)
    {
        try {
            $bazaars = $this->service->filter($request->validated());
            return $this->successResponse("all bazaars listed", BazaarResource::collection($bazaars));
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBazaarRequest $request)
    {
        try {
            $image = $request->file("image") ?? null;
            $data = $request->validated();
            unset($data['image']);
            $bazaar = $this->service->create($data, $image);
            return $this->successResponse("bazaar created successfully.", $bazaar);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show(Bazaar $bazaar)
    {
        $this->authorize('view', $bazaar);

        try {
            $userId = Auth::id();

            $bazaar->load([
                'address',
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

            // fetch products related via pivot table
            $products = $bazaar->products()->with(['image', 'category', 'store'])->get();

            // group products by category
            $categoriesGrouped  = $products
                ->groupBy('product_category_id')
                ->map(function ($products) {
                    $category = $products->first()->category;
                    return [
                        'id'       => $category->id,
                        'name'     => $category->name,
                        'products' => ProductCardResource::collection($products->take(2)),
                    ];
                });

            // categories only
            $categories = $categoriesGrouped->map(fn($c) => [
                'id'   => $c['id'],
                'name' => $c['name'],
            ])->values();

            // flatten product lists
            $productsList = $categoriesGrouped
                ->flatMap(fn($c) => $c['products']->toArray(request()))
                ->values();

            $extra = [
                'categories' => $categories,
                'products'   => $productsList
            ];

            return $this->successResponse("bazaar details", new BazaarResource($bazaar, null, $extra));
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * get the product of category
     */
    public function getCategoryProducts(GetCategoryIdRequest $request, Bazaar $bazaar)
    {
        return $this->successResponse("products listed", ProductCardResource::collection($this->service->getCategoryProducts($bazaar, $request->validated()['category_id'])));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBazaarRequesst $request, Bazaar $bazaar)
    {
        try {
            $image = $request->file("image") ?? null;
            $data = $request->validated();
            unset($data['image']);
            $bazaar = $this->service->update($bazaar, $data, $image);
            return $this->successResponse("bazaar updated", new BazaarResource($bazaar));
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bazaar $bazaar)
    {
        $this->authorize('delete', $bazaar);

        try {
            $this->service->delete($bazaar);
            return $this->successResponse("bazaar deleted");
        } catch (Exception $e) {
            return $this->errorResponse("failed to delete bazaar", 500);
        }
    }

    /**
     * View own bazaars
     */
    public function MyBazaars()
    {
        $this->authorize('viewOwn', Bazaar::class);

        try {
            $user = Auth::user();

            $bazaars = $user->bazaars->load(['address', 'image']);
            return $this->successResponse("own bazaars listed", BazaarResource::collection($bazaars));
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Add a comment to the bazaar.
     */
    public function addComment(StoreCommentRequest $request, Bazaar $bazaar)
    {
        try {
            $comment = $this->commentService->create($bazaar, $request->validated());
            return $this->successResponse("comment added", new CommentResource($comment->load('user')));
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Return all bazaar comments.
     */
    public function comments(Bazaar $bazaar)
    {
        $this->authorize('viewComments', Bazaar::class);

        try {
            $comments = $this->commentService->all($bazaar);
            return $this->successResponse("comments listed", CommentResource::collection($comments));
        } catch (Exception $e) {
            return $this->errorResponse("failed to list comments", 500);
        }
    }

    public function toggleBazaar(Bazaar $bazaar, FavoriteService $service)
    {
        try {
            $added = $service->toggleFavorite($bazaar, Auth::user());

            return $this->successResponse("favorite added successfully");
        } catch (Exception $e) {
            return $this->errorResponse("failed to add to favorite");
        }
    }
}
