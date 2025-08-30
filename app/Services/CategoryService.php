<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class CategoryService
{
    protected string $class;
    public function __construct(string $class)
    {
        $this->class = $class;
    }

    /**
     * search store categories based on a subname
     * 
     * @param  array $criteria - part of the store category name
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function list(array $criteria)
    {
        if (isset($criteria['name'])) {
            $categories = $this->class::where('name', 'like', '%' . $criteria['name'] . '%')->take(3)->get();
            return $categories;
        } else return "";
    }

    /**
     * create new store category
     * 
     * @param string $name - the name of new store category
     * @return Resource
     */
    public function create(string $name)
    {
        $category = $this->class::create([
            'name' => $name,
        ]);

        return $category;
    }

    /**
     * delete the store category
     * 
     * @param Model
     *  $model
     */
    public function delete(Model $model)
    {
        $model->delete();
    }
}
