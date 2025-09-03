<?php

namespace App\Policies;

use app\Contracts\Roles;
use App\Models\Address;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AddressPolicy
{
    public function before(User $user)
    {
        if ($user->hasRole(Roles::ADMIN))
            return true;
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Address $address): bool
    {
        return $user->hasRole(Roles::INSPECTOR) && $user->id == $address;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole([Roles::INSPECTOR, Roles::SELLER]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Address $address): bool
    {
        return $user->hasRole([Roles::INSPECTOR, Roles::SELLER]) && $user->id == $address->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Address $address): bool
    {
        return $user->hasRole([Roles::INSPECTOR, Roles::SELLER]) && $user->id == $address;
    }

    /**
     * Determine whether the user can show the model.
     */
    public function showOwnAddresses(User $user): bool
    {
        return $user->hasRole([Roles::INSPECTOR, Roles::SELLER]);
    }
}
