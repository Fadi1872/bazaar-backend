<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreCategory extends Model
{
    protected $fillable = [
        'name'
    ];

    /**
     * get the stores that this category belongs to
     */
    public function stores()
    {
        return $this->hasMany(Store::class, 'store_category_id');
    }
}
