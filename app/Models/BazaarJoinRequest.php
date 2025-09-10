<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BazaarJoinRequest extends Model
{
    protected $fillable = [
        'bazaar_id',
        'status',
        'message',
        'reviewed_at',
    ];

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
    public function products()
    {
        return $this->hasMany(BazaarRequestProduct::class, 'join_request_id');
    }
}
