<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BazaarCategory extends Model
{
    protected $fillable = [
        'name'
    ];

    /**
     * get products of category
     */
    public function bazaars()
    {
        return $this->hasMany(Bazaar::class, 'category_id');
    }
}
