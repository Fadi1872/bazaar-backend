<?php

namespace App\Services;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    /**
     * Get all orders for the authenticated user
     */
    public function getUserOrders()
    {
        $user = Auth::user();

        $orders = Order::with([
            'items.product.image',
            'address',
            'paymentMethod',
        ])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);
        return OrderResource::collection($orders->load('paymentMethod'));
    }

    /**
     * Get a single order with all details
     */
    public function getUserOrder(int $orderId)
    {
        $user = Auth::user();

        $order = Order::with([
            'items.product.image',
            'address',
            'paymentMethod',
        ])
            ->where('user_id', $user->id)
            ->findOrFail($orderId);
        return new OrderResource($order);
    }
}
