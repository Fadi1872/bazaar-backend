<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BazaarJoinRequestStoreRequest;
use App\Http\Requests\BazaarJoinRequestUpdateRequest;
use App\Models\Bazaar;
use App\Models\BazaarJoinRequest;
use App\Services\RequestJoinBazaarService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class BazaarJoinRequestController extends Controller
{
    use AuthorizesRequests;
    protected $service;

    public function __construct(RequestJoinBazaarService $service)
    {
        $this->service = $service;
    }

    /**
     * List all requests for a specific bazaar
     */
    public function index(Bazaar $bazaar)
    {
        $this->authorize('viewRequests', $bazaar);

        $requests = $this->service->showBazaarRequests($bazaar);

        return response()->json([
            'message' => 'Bazaar join requests listed',
            'data' => $requests
        ]);
    }

    /**
     * Show a specific request
     */
    public function show(BazaarJoinRequest $request)
    {
        $this->authorize('view', $request);

        return response()->json([
            'message' => 'Bazaar join request details',
            'data' => $this->service->showBazaarRequests($request->bazaar)
        ]);
    }

    /**
     * Create a new join request
     */
    public function store(BazaarJoinRequestStoreRequest $request, Bazaar $bazaar)
    {
        try {
            $requestModel = $this->service->createRequest($bazaar, $request->validated());

            return $this->successResponse("request created successfully!", $requestModel);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Update an existing request
     */
    public function update(BazaarJoinRequestUpdateRequest $request, BazaarJoinRequest $joinRequest)
    {
        $updatedRequest = $this->service->updateRequest($joinRequest, $request->validated());

        return response()->json([
            'message' => 'Request updated successfully',
            'data' => $updatedRequest
        ]);
    }

    /**
     * Delete a join request
     */
    public function destroy(BazaarJoinRequest $joinRequest)
    {
        try {
            $this->service->deleteRequest($joinRequest);

            return $this->successResponse("Request deleted successfully");
        } catch (Exception $e) {
            return $this->errorResponse("failed to delete request.");
        }
    }

    /**
     * Accept a request and add its products to the bazaar
     */
    public function accept(BazaarJoinRequest $joinRequest)
    {
        try {
            $this->service->acceptRequest($joinRequest);

            return $this->successResponse("Request accepted and products added to bazaar");
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Reject a request
     */
    public function reject(BazaarJoinRequest $joinRequest)
    {
        try {
            $this->service->rejectRequest($joinRequest);

            return $this->successResponse("Request rejected successfully");
        } catch (Exception $e) {
            return $this->errorResponse("failed to reject request.");
        }
    }
}
