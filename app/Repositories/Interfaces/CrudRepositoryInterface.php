<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Builder;
use App\Models\BaseModel;

/**
 * Interface CrudRepositoryInterface.
 */
interface CrudRepositoryInterface
{
    /**
     * Find the model by the id.
     *
     * @param mixed $id
     *
     * @return BaseModel|null
     */
    public function find(mixed $id): ?BaseModel;

    /**
     * Find the model by the id or throw an exception.
     *
     * @param mixed $id
     *
     * @return BaseModel|null
     */
    public function findOrFail(mixed $id): ?BaseModel;

    /**
     * Create model with given attributes.
     *
     * @param array $attributes
     *
     * @return BaseModel
     */
    public function create(array $attributes): BaseModel;

    /**
     * Update model with given attributes.
     *
     * @param BaseModel $model
     * @param array $attributes
     *
     * @return bool
     */
    public function update(BaseModel $model, array $attributes): bool;

    /**
     * Update or create model with given attributes.
     *
     * @param array $attributes
     *
     * @return Builder|BaseModel
     */
    public function updateOrCreate(array $attributes): Builder|BaseModel;

    /**
     * Delete given model.
     *
     * @param BaseModel $model
     *
     * @return bool
     */
    public function delete(BaseModel $model): bool;

    /**
     * Force delete given model.
     *
     * @param BaseModel $model
     *
     * @return bool
     */
    public function forceDelete(BaseModel $model): bool;
}
