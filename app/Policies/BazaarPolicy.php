<?php

namespace App\Policies;

use app\Contracts\Permissions;
use app\Contracts\Roles;
use App\Models\Bazaar;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BazaarPolicy
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
        return $user->hasPermissionTo(Permissions::VIEW_ALL_BAZAARS);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Bazaar $bazaar): bool
    {
        return $user->hasPermissionTo(Permissions::VIEW_BAZAAR_DETAILS);
    }
    
    /**
     * Determine whether the user can view the model.
     */
    public function viewOwn(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::VIEW_OWN_BAZAARS);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::CREATE_BAZAAR);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Bazaar $bazaar): bool
    {
        return $user->hasPermissionTo(Permissions::UPDATE_BAZAAR) && $user->id == $bazaar->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bazaar $bazaar): bool
    {
        return $user->hasPermissionTo(Permissions::DELETE_BAZAAR) && $user->id == $bazaar->user_id;;
    }
}
