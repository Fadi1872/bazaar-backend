<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartService
{
    /**
     * Get or create the user's default cart
     */
    public function getUserCart($userId = null): Cart
    {
        $userId = $userId ?? Auth::id();

        return Cart::firstOrCreate([
            'user_id' => $userId,
        ]);
    }

    /**
     * Add an item to the cart
     */
    public function addItem(Product $product, int $quantity = 1): Cart
    {
        $cart = $this->getUserCart();

        $item = $cart->items()->where('product_id', $product->id)->first();

        $newQuantity = $item ? $item->quantity + $quantity : $quantity;

        if ($newQuantity > $product->stock_qty) {
            throw new \Exception("you have exceed the limited amount.");
        }

        if ($item) {
            $item->increment('quantity', $quantity);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity'   => $quantity,
            ]);
        }

        return $cart->load('items.product');
    }

    /**
     * Remove one unit of an item from the cart
     */
    public function removeOne(Product $product): Cart
    {
        $cart = $this->getUserCart();

        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            if ($item->quantity > 1) {
                $item->decrement('quantity');
            } else {
                $item->delete();
            }
        }

        return $cart->load('items.product');
    }

    /**
     * Delete an item completely from the cart
     */
    public function deleteItem(Product $product): Cart
    {
        $cart = $this->getUserCart();

        $cart->items()->where('product_id', $product->id)->delete();

        return $cart->load('items.product');
    }
}
