<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * update user data
     */
    public function update(User $user, User $userData): bool {
        return $user->id == $userData->id;
    }
}
