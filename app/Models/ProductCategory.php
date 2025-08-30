<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $fillable = [
        'name'
    ];

    /**
     * get the products that belong to the category
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
