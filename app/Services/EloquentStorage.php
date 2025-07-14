<?php

namespace App\Services;

use App\Contracts\StorageInterface;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class EloquentStorage implements StorageInterface
{
    /**
     * determains the model that will be used
     * 
     * @var string
     */
    protected string $modelClass;

    /**
     * Create a new class instance.
     */
    public function __construct(string $modelClass)
    {
        if (!is_subclass_of($modelClass, Model::class))
            throw new Exception('Provided class must be an instance of Illuminate\\Database\\Eloquent\\Model');

        $this->modelClass = $modelClass;
    }

    /**
     * retrieve all modelClass
     * 
     * @return array
     * @throws Exception
     */
    public function all(): array
    {
        try {
            return $this->modelClass::all()->toArray();
        } catch (Throwable $e) {
            throw new Exception("Failed to get all {$this->modelClass}");
        }
    }

    /**
     * paginate the modelClass
     * 
     * @param int perPage - number of items per page
     * 
     * @return Illuminate\Pagination\LengthAwarePaginator
     * @throws Exception
     */
    public function paginate(int $perPage = 15): array
    {
        try {
            return $this->modelClass::paginate($perPage)->toArray();
        } catch (Throwable $e) {
            throw new Exception("Failed to paginate {$this->modelClass}");
        }
    }

    /**
     * create new modelClass
     * 
     * @param array $data - item data to store
     * 
     * @return Illuminate\Database\Eloquent\Model
     * @throws Exception
     */
    public function store(array $data): Model
    {
        try {
            return $this->modelClass::create($data);
        } catch (Throwable $e) {
            throw new Exception("failed to store new {$this->modelClass}");
        }
    }

    /**
     * udpate the model
     * 
     * @param Model $model - model want to update
     * @param array $data - data want to be updated in the model
     * 
     * @return bool
     * @throws Exception
     */
    public function update(Model $model, array $data): bool
    {
        try {
            return $model->update($data);
        } catch (Throwable $e) {
            throw new Exception("failed to update {$this->modelClass}");
        }
    }

    /**
     * delete the model
     * 
     * @param Model $model - model want to delete
     * 
     * @return bool
     * @throws Exception
     */
    public function delete(Model $model): bool
    {
        try {
            return $model->delete();
        } catch (Throwable $e) {
            throw new Exception("failed to delete {$this->modelClass}");
        }
    }
}
