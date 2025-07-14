<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;

interface StorageInterface
{
    public function all();
    public function paginate(int $perPage = 15);
    public function store(array $data): Model;
    public function update(Model $model, array $data): bool;
    public function delete(Model $model): bool;
}
