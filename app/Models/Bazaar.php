<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bazaar extends Model
{
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'start_requesting_date',
        'end_requesting_date',
        'user_id',
        'address_id',
        'category_id',
        'positiveness'
    ];

    /**
     * return the user that created this bazaar
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * return the category of the bazaar
     */
    public function category()
    {
        return $this->belongsTo(BazaarCategory::class, "category_id");
    }

    /**
     * return the bazaar image
     */
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
