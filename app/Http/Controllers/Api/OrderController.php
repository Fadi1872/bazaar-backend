<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Exception;

class OrderController extends Controller
{
    protected OrderService $orders;

    public function __construct(OrderService $orders)
    {
        $this->orders = $orders;
    }

    /**
     * List all orders for the authenticated user
     */
    public function index()
    {
        try {
            $ordersData = $this->orders->getUserOrders();
            return $this->successResponse("user orders listed", $ordersData);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Show one order with details
     */
    public function show($id)
    {
        try {
            $orderData = $this->orders->getUserOrder($id);
            return $this->successResponse("user order detailes", $orderData);
        } catch (Exception $e) {
            return $this->errorResponse("failed to load order");
        }
    }
}
