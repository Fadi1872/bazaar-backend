<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * List all active payment methods
     */
    public function index()
    {
        $methods = PaymentMethod::where('is_active', true)->get();

        return $this->successResponse("Payment methods retrieved successfully", $methods);
    }
}
