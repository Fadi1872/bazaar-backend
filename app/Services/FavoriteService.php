<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FavoriteService
{
    public function toggleFavorite(Model $model, User $user)
    {
        $exists = $user->favorites()
            ->where('favoritable_id', $model->id)
            ->where('favoritable_type', get_class($model))
            ->exists();

        if ($exists) {
            $user->favorites()
                ->where('favoritable_id', $model->id)
                ->where('favoritable_type', get_class($model))
                ->delete();

            return false;
        } else {
            $user->favorites()->create([
                'user_id'         => Auth::id(),
                'favoritable_id' => $model->id,
                'favoritable_type' => get_class($model),
            ]);

            return true;
        }
    }
}
