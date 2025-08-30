<?php

namespace App\Policies;

use app\Contracts\Permissions;
use App\Models\StoreCategory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StoreCategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::VIEW_STORE_CATEGORY);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::CREATE_STORE_CATEGORY);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::DELETE_STORE_CATEGORY);
    }
}
