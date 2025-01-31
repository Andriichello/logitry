<?php

namespace App\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Filters\BaseFilter;
use App\Models\BaseModel;
use App\Queries\BaseQuery;
use App\Queries\Interfaces\IndexableInterface;
use App\Repositories\Interfaces\CrudRepositoryInterface;
use App\Repositories\Traits\AllowsFilters;
use App\Repositories\Traits\AllowsSorts;

/**
 * Class CrudRepository.
 */
abstract class CrudRepository extends BaseRepository implements
    CrudRepositoryInterface
{
    use AllowsSorts;
    use AllowsFilters;

    /**
     * Get query builder for the repository's model.
     *
     * @param Request|null $request
     *
     * @return BaseQuery
     * @throws Exception
     */
    public function builder(?Request $request = null): BaseQuery
    {
        return $this->applyRequestOnQuery(parent::builder(), $request);
    }

    /**
     * Apply `sorts`, `filters` and `index` rules on query.
     *
     * @param BaseQuery $query
     * @param Request|null $request
     *
     * @return BaseQuery
     * @throws Exception
     */
    protected function applyRequestOnQuery(BaseQuery $query, ?Request $request = null): BaseQuery
    {
        if ($query instanceof IndexableInterface) {
            $query->index($request ?? request());
        }

        if ($request) {
            $sorts = $this->getRequestedSorts($request);

            if (empty($sorts) && $this->hasAllowedSorts('id')) {
                $sorts['id'] = 'asc';
            }

            foreach ($sorts as $sort => $direction) {
                $table = $this->tableOfAllowedSort($sort) ?? $query->getModel()->getTable();
                $column = $this->columnOfAllowedSort($sort) ?? $sort;

                $query->orderBy(
                    empty($table) ? $column : $table . '.' . $column,
                    $direction
                );
            }

            $filters = $this->getRequestedFilters($request);

            foreach ($filters as $filter => $value) {
                if ($value === '') {
                    continue;
                }

                $class = $this->classOfAllowedFilter($filter);
                $table = $this->tableOfAllowedFilter($filter);
                $column = $this->columnOfAllowedFilter($filter);

                /** @var BaseFilter $logic */
                $logic = new $class($table ?? $query->getModel()->getTable());
                $logic->apply($query, $column ?? $filter, $value);
            }
        }

        return $query;
    }

    /**
     * Find the model by the id.
     *
     * @param mixed $id
     *
     * @return BaseModel|null
     */
    public function find(mixed $id): ?BaseModel
    {
        return $this->builder()->find($id);
    }

    /**
     * Find the model by the id or throw an exception.
     *
     * @param mixed $id
     *
     * @return BaseModel|null
     */
    public function findOrFail(mixed $id): ?BaseModel
    {
        return $this->builder()->findOrFail($id);
    }

    /**
     * Create model with given attributes.
     *
     * @param array $attributes
     *
     * @return BaseModel
     */
    public function create(array $attributes): BaseModel
    {
        return $this->builder()->create($attributes);
    }

    /**
     * Update model with given attributes.
     *
     * @param BaseModel $model
     * @param array $attributes
     *
     * @return bool
     */
    public function update(BaseModel $model, array $attributes): bool
    {
        return $model->update($attributes);
    }

    /**
     * Update or create model with given attributes.
     *
     * @param array $attributes
     *
     * @return Builder|BaseModel
     */
    public function updateOrCreate(array $attributes): Builder|BaseModel
    {
        return $this->builder()->updateOrCreate($attributes);
    }

    /**
     * Delete given model.
     *
     * @param BaseModel $model
     *
     * @return bool
     */
    public function delete(BaseModel $model): bool
    {
        return $model->delete();
    }

    /**
     * Force delete given model.
     *
     * @param BaseModel $model
     *
     * @return bool
     */
    public function forceDelete(BaseModel $model): bool
    {
        if (!$this->delete($model)) {
            return false;
        }

        return $model->forceDelete();
    }
}
