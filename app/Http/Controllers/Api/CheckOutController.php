<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckOutRequest;
use App\Services\CheckOutService;
use Exception;
use Illuminate\Http\Request;

class CheckOutController extends Controller
{
    protected CheckOutService $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    /**
     * Handle checkout
     */
    public function store(CheckOutRequest $request)
    {
        try {
            $order = $this->checkoutService->checkout(
                paymentMethodId: $request->payment_method_id,
                addressId: $request->address_id
            );

            return $this->successResponse("Order created successfully", $order->load('items.product', 'paymentMethod', 'address'));
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
