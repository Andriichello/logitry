<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\Helpers;
use App\Models\BaseModel;
use App\Queries\BaseQuery;
use App\Repositories\Interfaces\BaseRepositoryInterface;

/**
 * Class BaseRepository.
 */
abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * Repository's model class.
     *
     * @var BaseModel|string
     */
    protected BaseModel|string $model = BaseModel::class;

    /**
     * Get repository's model class.
     *
     * @return BaseModel|string
     */
    public function model(): BaseModel|string
    {
        return $this->model;
    }

    /**
     * Get new instance of repository's model class.
     *
     * @return BaseModel|null
     */
    public function instance(): ?BaseModel
    {
        $class = is_string($this->model)
            ? $this->model : get_class($this->model);

        return empty($class) ? null : new $class();
    }

    /**
     * Get query builder for the repository's model.
     *
     * @return BaseQuery
     */
    public function builder(): BaseQuery
    {
        return ($this->model)::query();
    }

    /**
     * Determine if repository's model is soft-deletable.
     *
     * @return bool
     */
    public function isSoftDeletable(): bool
    {
        return Helpers::usesTrait($this->model, SoftDeletes::class);
    }
}
