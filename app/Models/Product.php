<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'product_category_id',
        'price',
        'cost',
        'stock_qty',
        'show_in_store',
        'rating'
    ];

    /**
     * return the user that created this product
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * return the category of the product
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, "product_category_id");
    }

    /**
     * return the prodect image
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
     * Get the store that owns this product (through the user).
     */
    public function store()
    {
        return $this->hasOneThrough(
            Store::class,
            User::class,
            'id',
            'user_id',
            'user_id',
            'id'
        );
    }

    /**
     * Scope a query to only include available products.
     */
    public function scopeAvailable($query)
    {
        return $query->where('show_in_store', true)
            ->where('stock_qty', '>', 0);
    }
}
