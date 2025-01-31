<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;

/**
 * Trait WithPagination.
 */
trait WithPagination
{
    /**
     * Paginate query builder.
     *
     * @param Builder|EloquentBuilder $builder
     *
     * @return Builder|EloquentBuilder
     */
    public function paginateQuery(Builder|EloquentBuilder $builder): Builder|EloquentBuilder
    {
        $size = min($this->getPageSize(), $this->getDefaultPageSize());
        $number = $this->getPageNumber();

        if ($this->shouldOmitPagination()) {
            $size = $this->getOmitPageSize();
        }

        return $builder->forPage($number, $size);
    }

    /**
     * Paginate query builder and return the paginator.
     *
     * @param Builder|EloquentBuilder $builder
     *
     * @return Paginator|LengthAwarePaginator
     */
    public function paginate(Builder|EloquentBuilder $builder): Paginator|LengthAwarePaginator
    {
        $size = min($this->getPageSize(), $this->getDefaultPageSize());

        if ($this->shouldOmitPagination()) {
            $size = $this->getOmitPageSize();
        }

        $method = $this->getPaginationMethod();

        $numberParameter = config('pagination.number_parameter');
        $paginationParameter = config('pagination.pagination_parameter');

        $paginator = $builder
            ->{$method}($size, ['*'], $paginationParameter . '.' . $numberParameter)
            ->setPageName($paginationParameter . '[' . $numberParameter . ']')
            ->appends(Arr::except(request()->input(), $paginationParameter . '.' . $numberParameter));

        if (!is_null(config('pagination.base_url'))) {
            $paginator->setPath(config('pagination.base_url'));
        }

        return $paginator;
    }

    /**
     * Get the maximum size of the page for pagination.
     *
     * @return int
     */
    public function getMaxPageSize(): int
    {
        return config('pagination.max_results', 100);
    }

    /**
     * Get the default size of the page for pagination.
     *
     * @return int
     */
    public function getDefaultPageSize(): int
    {
        return config('pagination.default_size', 25);
    }

    /**
     * Get the name of method, which should be used on query builder
     * for the pagination.
     *
     * @return string
     */
    public function getPaginationMethod(): string
    {
        return config('pagination.use_simple_pagination', false)
            ? 'simplePaginate' : 'paginate';
    }

    /**
     * Get the value that determines if omit parameter should be ignored.
     *
     * @return bool
     */
    public function shouldIgnoreOmitPagination(): bool
    {
        return config('pagination.ignore_omit_pagination', true);
    }

    /**
     * Get the size of the page for pagination.
     *
     * @param Request|null $request
     *
     * @return int
     */
    public function getPageSize(?Request $request = null): int
    {
        $parameter = implode(
            '.',
            [
                config('pagination.pagination_parameter'),
                config('pagination.size_parameter')
            ]
        );

        return ($request ?? request())
            ->integer($parameter, $this->getDefaultPageSize());
    }

    /**
     * Get the number of the page for pagination.
     *
     * @param Request|null $request
     *
     * @return int
     */
    public function getPageNumber(?Request $request = null): int
    {
        $parameter = implode(
            '.',
            [
                config('pagination.pagination_parameter'),
                config('pagination.number_parameter')
            ]
        );

        return ($request ?? request())
            ->integer($parameter, 1);
    }

    /**
     * Get the value that determines if pagination should be omitted
     * (if all records should be returned).
     *
     * @param Request|null $request
     *
     * @return bool
     */
    public function shouldOmitPagination(?Request $request = null): bool
    {
        if ($this->shouldIgnoreOmitPagination()) {
            return false;
        }

        $parameter = implode(
            '.',
            [
                config('pagination.pagination_parameter'),
                config('pagination.omit_parameter')
            ]
        );

        $omit = ($request ?? request())
            ->integer($parameter);

        return $omit > 0;
    }

    /**
     * Get the maximum page size when the pagination should be omitted.
     *
     * @param Request|null $request
     *
     * @return int
     */
    public function getOmitPageSize(?Request $request = null): int
    {
        $parameter = implode(
            '.',
            [
                config('pagination.pagination_parameter'),
                config('pagination.omit_parameter')
            ]
        );

        $omit = ($request ?? request())
            ->integer($parameter);

        return $omit === 1 ? 1000000 : $omit;
    }
}
