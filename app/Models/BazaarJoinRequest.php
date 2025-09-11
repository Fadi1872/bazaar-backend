<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BazaarJoinRequest extends Model
{
    protected $fillable = [
        'bazaar_id',
        'user_id',
        'status',
        'message',
        'reviewed_at',
    ];

    /**
     * get the user that added the request
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The bazaar this request belongs to.
     */
    public function bazaar()
    {
        return $this->belongsTo(Bazaar::class);
    }

    /**
     * Products attached to this join request.
     */
    public function requestProducts()
    {
        return $this->hasMany(BazaarRequestProduct::class, 'join_request_id');
    }
}
