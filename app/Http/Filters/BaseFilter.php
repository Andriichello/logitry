<?php

namespace App\Http\Filters;

use Illuminate\Support\Str;
use App\Queries\BaseQuery;

/**
 * Class BaseFilter.
 */
class BaseFilter
{
    /**
     * Table on which the filter is applied.
     *
     * @var string|null
     */
    protected ?string $table = null;

    /**
     * BaseFilter constructor.
     */
    public function __construct(?string $table = null)
    {
        $this->table = $table;
    }

    /**
     * Apply filter to the given query builder instance.
     *
     * @param BaseQuery $query
     * @param string $column
     * @param mixed $value
     *
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    public function apply(BaseQuery $query, string $column, mixed $value): void
    {
        if (!empty($this->table) && !str_contains($column, '.')) {
            $column = Str::of($column)
                ->start($this->table . '.')
                ->value();
        }

        if (is_array($value)) {
            if (str_starts_with($value[0], '!')) {
                $value[0] = ltrim($value[0], '!');
                $not = true;
            }

            if (str_starts_with($value[0], '~')) {
                $value[0] = ltrim($value[0], '~');

                empty($not)
                    ? $query->whereLikeAny("%$column%", $value)
                    : $query->whereNotLikeAny("%$column%", $value);

                return;
            }

            if (str_starts_with($value[0], '<')) {
                $value[0] = ltrim($value[0], '<');
                $not = empty($not) ? false : $not;

                $query->where(function (BaseQuery $query) use ($not, $column, $value) {
                    foreach ($value as $val) {
                        empty($not)
                            ? $query->where($column, '<', $val)
                            : $query->whereNot($column, '<', $val);
                    }
                });

                return;
            }

            if (str_starts_with($value[0], '>')) {
                $value[0] = ltrim($value[0], '>');
                $not = empty($not) ? false : $not;

                $query->where(function (BaseQuery $query) use ($not, $column, $value) {
                    foreach ($value as $val) {
                        empty($not)
                            ? $query->where($column, '>', $val)
                            : $query->whereNot($column, '>', $val);
                    }
                });

                return;
            }

            empty($not)
                ? $query->whereIn($column, $value)
                : $query->whereNotIn($column, $value);

            return;
        }

        if (is_string($value)) {
            if (str_starts_with($value, '!')) {
                $value = ltrim($value, '!');
                $not = true;
            }

            if (str_starts_with($value, '~')) {
                $value = ltrim($value, '~');

                empty($not)
                    ? $query->whereLike($column, $value)
                    : $query->whereNotLike($column, $value);

                return;
            }

            if (str_starts_with($value, '<')) {
                $value = ltrim($value, '<');
                $not = empty($not) ? false : $not;

                empty($not)
                    ? $query->where($column, '<', $value)
                    : $query->whereNot($column, '<', $value);

                return;
            }

            if (str_starts_with($value, '>')) {
                $value = ltrim($value, '>');
                $not = empty($not) ? false : $not;

                empty($not)
                    ? $query->where($column, '>', $value)
                    : $query->whereNot($column, '>', $value);

                return;
            }
        }

        $query->where($column, empty($not) ? '=' : '!=', $value);
    }
}
