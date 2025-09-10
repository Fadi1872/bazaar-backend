<?php

namespace App\Policies;

use app\Contracts\Permissions;
use app\Contracts\Roles;
use App\Models\Store;
use App\Models\User;

class StorePolicy
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
        return $user->hasRole(Roles::INSPECTOR);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return $user->can(Permissions::VIEW_STORE_DETAILS);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function viewOwn(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::VIEW_OWN_STORE);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(Roles::SELLER) && !$user->store()->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Store $store): bool
    {
        return $user->hasRole(Roles::SELLER) && $store->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Store $store): bool
    {
        return $user->hasRole(Roles::SELLER) && $store->user_id == $user->id;
    }

    /**
     * Determine whether the user can comment on the store.
     */
    public function comment(User $user): bool
    {
        return $user->can(Permissions::COMMENT_ON_STORE);
    }

    /**
     * Determine whether the user can view all comments on the store.
     */
    public function viewComments(User $user): bool
    {
        return $user->can(Permissions::VIEW_STORE_COMMENTS);
    }
}
