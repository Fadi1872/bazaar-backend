<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddItemRequest;
use App\Models\Cart;
use App\Models\Product;
use App\Services\CartService;
use Exception;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Get the authenticated user’s cart
     */
    public function index()
    {
        try {
            $cart = $this->cartService->getUserCart();

            return $this->successResponse('Cart retrieved successfully.', $cart->load('items.product'));
        } catch (Exception $e) {
            return $this->errorResponse('faild to retieve cart.');
        }
    }

    /**
     * Add a product to the cart
     */
    public function addItem(Product $product)
    {
        try {
            $cart = $this->cartService->addItem($product);

            return $this->successResponse('Product added to cart successfully.', $cart);
        } catch (Exception $e) {
            return $this->errorResponse('failed to add product.');
        }
    }

    /**
     * Remove one unit of a product from the cart
     */
    public function removeOne(Product $product)
    {
        try {
            $cart = $this->cartService->removeOne($product);

            return $this->successResponse('One item removed from cart.', $cart);
        } catch (Exception $e) {
            return $this->errorResponse('failed to remove item.');
        }
    }

    /**
     * Delete an item completely from the cart
     */
    public function deleteItem(Product $product)
    {
        try {
            $cart = $this->cartService->deleteItem($product);

            return $this->successResponse('Product removed from cart completely.', $cart);
        } catch (Exception $e) {
            return $this->errorResponse('failed to remove product.');
        }
    }
}
