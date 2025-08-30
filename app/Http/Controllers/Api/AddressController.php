<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Services\AddressService;
use App\Services\EloquentStorage;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AddressController extends Controller
{
    use AuthorizesRequests;

    protected AddressService $service;
    public function __construct()
    {
        $this->service = new AddressService(new EloquentStorage(Address::class));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Address::class);
        try {
            $addresses = $this->service->all();
            return $this->successResponse("all addresses listed", $addresses);
        } catch (Exception $e) {
            return $this->errorResponse("failed to list addresses", 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAddressRequest $request)
    {
        try {
            $address = $this->service->createAndAddToUserAddresses($request->validated());
            return $this->successResponse("address created successfully and attached to you.", new AddressResource($address));
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Address $address)
    {
        $this->authorize('view', $address);
        return $this->successResponse("address details", new AddressResource($address));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAddressRequest $request, Address $address)
    {
        try {
            $address = $this->service->updateAddressData($request->validated(), $address);
            return $this->successResponse("address data updated successfully", new AddressResource($address));
        } catch (Exception $e) {
            return $this->errorResponse("failed to update the address data.", 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address)
    {
        try {
            $address = $this->service->deleteAddress($address);
            return $this->successResponse("address data deleted successfully");
        } catch (Exception $e) {
            return $this->errorResponse("failed to delete the address data.", 500);
        }
    }

    /**
     * return user addresses
     */
    public function userAddresses()
    {
        $this->authorize('showOwnAddresses', Address::class);
        try {
            $addresses = $this->service->showCurrentUserAddresses();
            return $this->successResponse("user addresses listed.", AddressResource::collection($addresses));
        } catch (Exception $e) {
            return $this->errorResponse("failed to get the addresses data.", 500);
        }
    }
}
