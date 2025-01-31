<?php

namespace App\Http\Filters;

use App\Queries\BaseQuery;
use Illuminate\Database\Query\Builder;

/**
 * Class ComaSeparatedFilter.
 */
class ComaSeparatedFilter extends BaseFilter
{
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
        $cast = "string_to_array($column, ',')";

        if (is_array($value)) {
            if (str_starts_with($value[0], '~')) {
                parent::apply($query, $column, $value);
                return;
            }

            $query->whereNested(function (Builder $nested) use ($cast, $value) {
                foreach ($value as $val) {
                    $nested->orWhereRaw("$val::text = any($cast)");
                }
            });

            return;
        }

        if (str_starts_with($value, '~')) {
            parent::apply($query, $column, $value);
            return;
        }

        $query->whereRaw("$value::text = any($cast)");
    }
}
