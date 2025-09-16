<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'store_category_id',
        'location_type',
        'address_id',
        'rating'
    ];

    /**
     * get the owner of the store
     */
    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * get the category of the store
     */
    public function category()
    {
        return $this->belongsTo(StoreCategory::class, 'store_category_id');
    }

    /**
     * get the address details
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * get the store image
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

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    /**
     * get the store products
     */
    public function products()
    {
        return $this->hasManyThrough(
            Product::class,
            User::class,
            'id',
            'user_id',
            'user_id',
            'id'
        )->where('show_in_store', true)
            ->where('stock_qty', '>', 0);
    }

    public function isFavoritedBy($userId)
    {
        return $this->favorites()->where('user_id', $userId)->exists();
    }
}
