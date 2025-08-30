<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'body',
        'rating',
        'sentiment'
    ];

    public function store()
    {
        return $this->morphTo();
    }

    /**
     * return the user associated with this comment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * return the users that liked this comment
     */
    public function likes()
    {
        return $this->belongsToMany(User::class, 'comment_likes')->withTimestamps();
    }

    /**
     * checks if the comment is already liked by the user
     */
    public function isLikedBy(User $user)
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}
