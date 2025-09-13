<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckOutService
{
    /**
     * Checkout and create an order
     */
    public function checkout(int $paymentMethodId, int $addressId, float $deliveryFee = 0, float $taxRate = 0.05): Order
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            throw new \Exception("Cart is empty");
        }

        return DB::transaction(function () use ($user, $cart, $addressId, $paymentMethodId, $deliveryFee, $taxRate) {
            $order = Order::create([
                'user_id' => $user->id,
                'payment_method_id' => $paymentMethodId,
                'address_id' => $addressId,
                'status' => 'pending',
                'subtotal' => 0,
                'delivery_fee' => $deliveryFee,
                'taxes' => 0,
                'total_price' => 0,
            ]);

            foreach ($cart->items as $cartItem) {
                $order->items()->create([
                    'product_id' => $cartItem->product_id,
                    'bazaar_id' => $cartItem->bazaar_id ?? null,
                    'store_id' => $cartItem->product->store->id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                    'total' => $cartItem->product->price * $cartItem->quantity,
                ]);
            }

            $order->calculateTotals($deliveryFee, $taxRate);

            $cart->items()->delete();

            return $order;
        });
    }
}
