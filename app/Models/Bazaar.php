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
        'location_type',
        'category_id',
        'positiveness'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'start_requesting_date' => 'datetime',
        'end_requesting_date' => 'datetime',
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

    /**
     * get the comments
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * get the address details
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * A bazaar has many products
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'bazaar_product')
            ->withTimestamps();
    }

    /**'
     * get the join reqeusts
     */
    public function joinRequests()
    {
        return $this->hasMany(BazaarJoinRequest::class);
    }
}
