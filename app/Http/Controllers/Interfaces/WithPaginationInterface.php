<?php

namespace App\Http\Controllers\Interfaces;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

/**
 * Interface WithPaginationInterface.
 */
interface WithPaginationInterface
{
    /**
     * Paginate query builder.
     *
     * @param Builder|EloquentBuilder $builder
     *
     * @return Builder|EloquentBuilder
     */
    public function paginateQuery(Builder|EloquentBuilder $builder): Builder|EloquentBuilder;

    /**
     * Paginate query builder and return the paginator.
     *
     * @param Builder|EloquentBuilder $builder
     *
     * @return Paginator|LengthAwarePaginator
     */
    public function paginate(Builder|EloquentBuilder $builder): Paginator|LengthAwarePaginator;

    /**
     * Get the size of the page for pagination.
     *
     * @param Request|null $request
     *
     * @return int
     */
    public function getPageSize(?Request $request = null): int;

    /**
     * Get the number of the page for pagination.
     *
     * @param Request|null $request
     *
     * @return int
     */
    public function getPageNumber(?Request $request = null): int;

    /**
     * Get the value that determines if pagination should be omitted
     * (if all records should be returned).
     *
     * @param Request|null $request
     *
     * @return bool
     */
    public function shouldOmitPagination(?Request $request = null): bool;

    /**
     * Get the maximum page size when the pagination should be omitted.
     *
     * @param Request|null $request
     *
     * @return int
     */
    public function getOmitPageSize(?Request $request = null): int;
}
