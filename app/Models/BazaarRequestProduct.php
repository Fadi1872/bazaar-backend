<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BazaarRequestProduct extends Model
{
    protected $fillable = [
        'join_request_id',
        'product_id',
    ];

    /**
     * The join request this product belongs to.
     */
    public function joinRequest()
    {
        return $this->belongsTo(BazaarJoinRequest::class, 'join_request_id');
    }

    /**
     * The actual product being requested.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
