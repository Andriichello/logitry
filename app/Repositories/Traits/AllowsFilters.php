<?php

namespace App\Repositories\Traits;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Helpers\Helpers;
use App\Http\Filters\BaseFilter;
use App\Repositories\CrudRepository;

/**
 * Trait AllowsFilters.
 *
 * Contains logic for getting the list of filters,
 * which should be applied for given request.
 * Throws exception when asked filter is not allowed.
 *
 * By default, all model's fillable columns along with
 * its primary key and timestamps will be allowed.
 *
 * @mixin CrudRepository
 */
trait AllowsFilters
{
    /**
     * Determines if all fillable columns as well as
     * timestamps and identifiers should be allowed
     * to filter on.
     *
     * @var bool
     */
    protected bool $allowAllFilters = true;

    /**
     * List of filters allowed on request.
     *
     * @var array<int|string, string|BaseFilter|array{string, ?string, ?string}>
     */
    protected array $allowedFilters = [];

    /**
     * Get list of filters allowed on request.
     *
     * @return array<int, string>
     */
    public function listOfAllowedFilters(): array
    {
        $allowed = $this->allowedFilters ?? [];

        if (!$this->allowAllFilters && count($allowed)) {
            return $allowed;
        }

        $all = array_merge($allowed, $this->instance()?->listOfColumns() ?? []);
        $list = [];

        foreach ($all as $key => $value) {
            $list[] = is_string($key) ? $key : $value;
        }

        return $list;
    }

    /**
     * Get class for the given filter.
     *
     * @param string $filter
     *
     * @return string
     */
    public function classOfAllowedFilter(string $filter): string
    {
        $entry = data_get($this->allowedFilters ?? [], $filter);

        if (is_string($entry)) {
            return $entry;
        }

        return data_get($entry, 0) ?? BaseFilter::class;
    }

    /**
     * Get column for the given filter.
     *
     * @param string $filter
     *
     * @return string|null
     */
    public function columnOfAllowedFilter(string $filter): ?string
    {
        $entry = data_get($this->allowedFilters ?? [], $filter);
        return data_get($entry, 1);
    }

    /**
     * Get table for the given filter.
     *
     * @param string $filter
     *
     * @return string|null
     */
    public function tableOfAllowedFilter(string $filter): ?string
    {
        $entry = data_get($this->allowedFilters ?? [], $filter);
        return data_get($entry, 2);
    }

    /**
     * Determines if all given filters are allowed on request.
     *
     * @param string|array $names
     *
     * @return bool
     */
    public function hasAllowedFilters(string|array $names): bool
    {
        $names = Arr::wrap($names);
        $allowed = $this->listOfAllowedFilters();

        foreach ($names as $filter) {
            if (!in_array($filter, $allowed)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get filter value. If it's not allowed then
     * an exception will be thrown.
     *
     * @param array $filters
     * @param string $name
     *
     * @return mixed
     * @throws Exception
     */
    public function getAllowedFilter(array $filters, string $name): mixed
    {
        if (!$this->hasAllowedFilters($name)) {
            $message = "The '$name' filter is not allowed on "
                . static::class;

            throw new Exception($message);
        }

        $values = explode(',', $filters[$name]);

        return count($values) === 1 ? $values[0] : $values;
    }

    /**
     * Get an array of filter keys to values. By default,
     * all allowed filters will be returned. If given filters
     * are not allowed then an exception will be thrown.
     *
     * @param array $filters
     * @param string|array|null $names
     *
     * @return array
     * @throws Exception
     */
    public function getAllowedFilters(array $filters, string|array $names = null): array
    {
        $names = $names !== null ? Arr::wrap($names)
            : $this->listOfAllowedFilters();

        $values = [];

        foreach ($names as $name) {
            if (key_exists($name, $filters)) {
                $values[$name] = $this->getAllowedFilter($filters, $name);
            }
        }

        return $values;
    }

    /**
     * Get an array of requested filters. If requested filters
     * are not allowed then an exception will be thrown.
     *
     * @param Request $request
     *
     * @return array
     * @throws Exception
     */
    public function getRequestedFilters(Request $request): array
    {
        return $this->getAllowedFilters(
            $filters = Helpers::requestedFilters($request),
            array_keys($filters)
        );
    }
}
