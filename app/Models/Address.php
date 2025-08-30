<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        "city",
        "phone_number",
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

    /**
     * get the associated store
     */
    public function store()
    {
        return $this->hasOne(Store::class);
    }
}
