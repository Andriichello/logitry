<?php

namespace App\Http\Requests\Interfaces;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\BaseModel;
use App\Queries\BaseQuery;

/**
 * Interface WithTargetInterface.
 */
interface WithTargetInterface
{
    /**
     * Get target id.
     *
     * @return mixed
     */
    public function getId(): mixed;

    /**
     * Set target id.
     *
     * @param mixed $value
     *
     * @return static
     */
    public function setId(mixed $value): static;

    /**
     * Get query for finding the target model.
     *
     * @param BaseModel|string $model
     *
     * @return BaseQuery
     */
    public function targetQuery(BaseModel|string $model): BaseQuery;

    /**
     * Get target model.
     *
     * @param BaseModel|string $model
     *
     * @return BaseModel|null
     */
    public function target(BaseModel|string $model): ?BaseModel;

    /**
     * Get target model or fail.
     *
     * @param BaseModel|string $model
     *
     * @return BaseModel
     * @throws ModelNotFoundException
     */
    public function targetOrFail(BaseModel|string $model): BaseModel;
}
