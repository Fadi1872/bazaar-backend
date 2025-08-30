<?php

namespace App\Policies;

use app\Contracts\Permissions;
use app\Contracts\Roles;
use App\Models\Product;
use App\Models\User;

class ProductPolicy
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
        return $user->hasPermissionTo(Permissions::VIEW_ALL_PRODUCTS);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::VIEW_PRODUCT_DETAILS);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::CREATE_PRODUCTS);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->hasPermissionTo(Permissions::UPDATE_PRODUCTS) && $user->id == $product->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->hasPermissionTo(Permissions::DELETE_PRODUCTS) && $user->id == $product->user_id;
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
