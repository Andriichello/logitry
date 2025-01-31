<?php

namespace App\Repositories\Interfaces;

use App\Models\BaseModel;
use App\Queries\BaseQuery;

/**
 * Interface BaseRepositoryInterface.
 */
interface BaseRepositoryInterface
{
    /**
     * Get repository's model class.
     *
     * @return BaseModel|string
     */
    public function model(): BaseModel|string;

    /**
     * Get new instance of repository's model class.
     *
     * @return BaseModel|null
     */
    public function instance(): ?BaseModel;

    /**
     * Get query builder for the repository's model.
     *
     * @return BaseQuery
     */
    public function builder(): BaseQuery;

    /**
     * Determine if repository's model is soft-deletable.
     *
     * @return bool
     */
    public function isSoftDeletable(): bool;
}
