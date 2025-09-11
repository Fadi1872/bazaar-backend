<?php

namespace App\Services;

use App\Models\Bazaar;
use App\Models\BazaarJoinRequest;
use App\Models\BazaarRequestProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestJoinBazaarService
{
    protected function ensureRequestIsOpen(Bazaar $bazaar)
    {
        $now = now();

        if (!$bazaar->start_requesting_date || !$bazaar->end_requesting_date) {
            throw new \Exception("Requesting period is not defined for this bazaar.");
        }

        if ($now->lt($bazaar->start_requesting_date) || $now->gt($bazaar->end_requesting_date)) {
            throw new \Exception("You can only create or modify requests during the requesting period.");
        }
    }

    /**
     * Show all join requests for a bazaar
     */
    public function showBazaarRequests(Bazaar $bazaar)
    {
        return $bazaar->joinRequests()
            ->with(['products', 'products.image', 'products.store'])
            ->latest()
            ->get();
    }

    /**
     * Create a new join request
     */
    public function createRequest(Bazaar $bazaar, array $data)
    {
        $this->ensureRequestIsOpen($bazaar);

        return DB::transaction(function () use ($bazaar, $data) {
            $request = BazaarJoinRequest::create([
                'bazaar_id' => $bazaar->id,
                'user_id' => Auth::id(),
                'message' => $data['message'] ?? '',
                'status' => 'pending',
            ]);

            if (!empty($data['products'])) {
                foreach ($data['products'] as $productId) {
                    BazaarRequestProduct::create([
                        'join_request_id' => $request->id,
                        'product_id' => $productId,
                    ]);
                }
            }

            return $request->load('requestProducts');
        });
    }

    /**
     * Show a single join request
     */
    public function showRequest(BazaarJoinRequest $request)
    {
        return $request->load(['requestProducts', 'bazaar']);
    }

    /**
     * Update a join request (message and products)
     */
    public function updateRequest(BazaarJoinRequest $request, array $data)
    {
        $this->ensureRequestIsOpen($request->bazaar);

        return DB::transaction(function () use ($request, $data) {
            $request->update([
                'message' => $data['message'] ?? $request->message,
            ]);

            if (isset($data['products'])) {
                $request->requestProducts()->delete();

                foreach ($data['products'] as $productId) {
                    BazaarRequestProduct::create([
                        'join_request_id' => $request->id,
                        'product_id' => $productId,
                    ]);
                }
            }

            return $request->load('requestProducts');
        });
    }

    /**
     * Delete a join request
     */
    public function deleteRequest(BazaarJoinRequest $request)
    {
        $this->ensureRequestIsOpen($request->bazaar);

        $request->delete();
        return true;
    }

    /**
     * Accept the request and add its products to the bazaar
     */
    public function acceptRequest(BazaarJoinRequest $request)
    {
        $this->ensureRequestIsOpen($request->bazaar);

        if ($request->status !== 'pending') {
            throw new \Exception("Request already processed.");
        }

        return DB::transaction(function () use ($request) {
            $request->update([
                'status' => 'accepted',
                'reviewed_at' => Carbon::now(),
            ]);

            $products = $request->requestProducts->pluck("product_id")->toArray();

            $bazaar = $request->bazaar;
            $bazaar->products()->syncWithoutDetaching($products);

            return $request->load(['requestProducts', 'bazaar']);
        });
    }

    /**
     * Reject the request
     */
    public function rejectRequest(BazaarJoinRequest $request)
    {
        $this->ensureRequestIsOpen($request->bazaar);

        if ($request->status !== 'pending') {
            throw new \Exception("Request already processed.");
        }

        $request->update([
            'status' => 'rejected',
            'reviewed_at' => Carbon::now(),
        ]);

        return $request;
    }
}
