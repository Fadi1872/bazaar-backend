<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        "city",
        "street",
        "latitude",
        "longitude",
        "label",
        "user_id"
    ];


    /**
     * get the user who created this address
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
