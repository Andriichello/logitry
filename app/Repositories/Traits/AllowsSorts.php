<?php

namespace App\Repositories\Traits;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Helpers\Helpers;
use App\Repositories\CrudRepository;

/**
 * Trait AllowsSorts.
 *
 * Contains logic for getting the list of sorts,
 * which should be applied for given request.
 * Throws exception when asked sort is not allowed.
 *
 * By default, all model's fillable columns along with
 * its primary key and timestamps will be allowed.
 *
 * @mixin CrudRepository
 */
trait AllowsSorts
{
    /**
     * Determines if all fillable columns as well as
     * timestamps and identifiers should be allowed
     * to sort by.
     *
     * @var bool
     */
    protected bool $allowAllSorts = true;

    /**
     * List of sorts allowed on request.
     *
     * @var string[]|array<string, string[]>
     */
    protected array $allowedSorts = [];

    /**
     * Get list of sorts allowed on request.
     *
     * @return array<int, string>
     */
    public function listOfAllowedSorts(): array
    {
        $allowed = $this->allowedSorts ?? [];

        if (!$this->allowAllSorts && count($allowed)) {
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
     * Get column for the given sort.
     *
     * @param string $sort
     *
     * @return string|null
     */
    public function columnOfAllowedSort(string $sort): ?string
    {
        $entry = data_get($this->allowedSorts ?? [], $sort);
        return data_get($entry, 0);
    }

    /**
     * Get table for the given sort.
     *
     * @param string $sort
     *
     * @return string|null
     */
    public function tableOfAllowedSort(string $sort): ?string
    {
        $entry = data_get($this->allowedSorts ?? [], $sort);
        return data_get($entry, 1);
    }

    /**
     * Determines if all given sorts are allowed on request.
     *
     * @param string|array $names
     *
     * @return bool
     */
    public function hasAllowedSorts(string|array $names): bool
    {
        $allowed = $this->listOfAllowedSorts();
        $names = Arr::wrap($names);

        foreach ($names as $name) {
            if (!in_array($name, $allowed)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get sort value. If it's not allowed then
     * an exception will be thrown.
     *
     * @param array $sorts
     * @param string $name
     *
     * @return mixed
     * @throws Exception
     */
    public function getAllowedSort(array $sorts, string $name): mixed
    {
        if (!$this->hasAllowedSorts($name)) {
            $message = "Sorting by the '$name' is not allowed on "
                . static::class;

            throw new Exception($message);
        }

        $values = explode(',', $sorts[$name]);

        return count($values) === 1 ? $values[0] : $values;
    }

    /**
     * Get an array of sort columns to sort directions. By default,
     * all allowed sorts will be returned. If given sorts
     * are not allowed then an exception will be thrown.
     *
     * @param array $sorts
     *
     * @return array
     * @throws Exception
     */
    public function getAllowedSorts(array $sorts): array
    {
        $allowed = $this->listOfAllowedSorts();
        $values = [];

        foreach ($allowed as $name) {
            if (key_exists($name, $sorts)) {
                $values[$name] = $this->getAllowedSort($sorts, $name);
            }
        }

        return $values;
    }

    /**
     * Get an array of requested sorts. If requested sorts
     * are not allowed then an exception will be thrown.
     *
     * @param Request $request
     *
     * @return array
     * @throws Exception
     */
    public function getRequestedSorts(Request $request): array
    {
        return $this->getAllowedSorts(Helpers::requestedSorts($request));
    }
}
