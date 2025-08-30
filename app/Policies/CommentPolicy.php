<?php

namespace App\Policies;

use app\Contracts\Permissions;
use app\Contracts\Roles;
use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function before(User $user)
    {
        if ($user->hasRole(Roles::ADMIN))
            return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Comment $comment): bool
    {
        return $user->hasPermissionTo(Permissions::UPDATE_COMMENT) && $user->id == $comment->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $user->hasPermissionTo(Permissions::DELETE_COMMENT) && $user->id == $comment->user_id;
    }

    /**
     * Determine whether the user can like the model.
     */
    public function like(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::LIKE_COMMENT);
    }

    /**
     * Determine whether the user can unlike the model.
     */
    public function unlike(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::UNLIKE_COMMENT);
    }
}
