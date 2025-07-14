<?php

namespace App\Services;

use App\Contracts\StorageInterface;
use App\Models\Address;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class AddressService
{
    protected StorageInterface $storage;
    /**
     * Create a new class instance.
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * return all addresses paginated 10 addresses by page
     * 
     * @return array
     */
    public function all()
    {
        $addresses = Address::latest()->paginate(10)->toArray();
        return $addresses;
    }

    /**
     * Store the new address and attach it to the current user.
     *
     * @param array $data
     * @return \App\Models\Address
     */
    public function createAndAddToUserAddresses(array $data)
    {
        $data["user_id"] = Auth::id();
        $address = $this->storage->store($data);
        return $address;
    }

    /**
     * return user addresses
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function showCurrentUserAddresses()
    {
        $user = Auth::user();
        $addresses = $user->addresses;
        return $addresses;
    }

    /**
     * update address data
     * 
     * @param array $data
     * @param Address $address
     * @return Address
     */
    public function updateAddressData(array $data, Address $address)
    {
        if (!isset($data['latitude'])) {
            unset($data['latitude']);
        }

        if (!isset($data['longitude'])) {
            unset($data['longitude']);
        }

        $address->update($data);
        return $address;
    }

    /**
     * delete the address
     * 
     * @param Address $address
     * @return void
     */
    public function deleteAddress(Address $address)
    {
        $this->storage->delete($address);
    }
}
