<?php

namespace App\Policies;

use app\Contracts\Permissions;
use App\Models\BazaarCategory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BazaarCategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::VIEW_BAZAAR_CATEGORY);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::CREATE_BAZAAR_CATEGORY);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::DELETE_BAZAAR_CATEGORY);
    }
}
