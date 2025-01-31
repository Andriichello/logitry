<?php

namespace App\Queries;

use App\Models\BaseModel;
use App\Queries\Interfaces\IndexableInterface;
use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;

/**
 * Class BaseQuery.
 *
 * @property BaseModel $model
 *
 * @method BaseQuery select($columns = ['*'])
 * @method BaseQuery whereKey($id)
 * @method BaseModel|null find($id, $columns = ['*'])
 * @method BaseModel findOrFail($id, $columns = ['*'])
 * @method BaseModel|null first($columns = ['*'])
 * @method BaseModel firstOrFail($columns = ['*'])
 * @method BaseModel make(array $attributes = [])
 * @method BaseModel create(array $attributes = [])
 * @method BaseModel updateOrCreate(array $attributes, array $values = [])
 */
class BaseQuery extends EloquentBuilder implements IndexableInterface
{
    /**
     * Apply index query conditions.
     *
     * @param Request $request
     *
     * @return BaseQuery
     * @SuppressWarnings(PHPMD)
     */
    public function index(Request $request): BaseQuery
    {
        return $this;
    }

    /**
     * Where column is like the given value.
     *
     * @param string $column
     * @param mixed $value
     * @param bool $caseSensitive
     * @param string $boolean
     * @param bool $not
     *
     * @return BaseQuery
     */
    public function whereLike(
        $column,
        $value,
        $caseSensitive = false,
        $boolean = 'and',
        $not = false
    ): BaseQuery {
        $operator = $caseSensitive ? 'like' : 'ilike';
        $this->where($column, $operator, "%$value%");

        return $this;
    }

    /**
     * Where column is not like the given value.
     *
     * @param string $column
     * @param mixed $value
     * @param bool $caseSensitive
     * @param string $boolean
     *
     * @return BaseQuery
     */
    public function whereNotLike(
        $column,
        $value,
        $caseSensitive = false,
        $boolean = 'and'
    ): BaseQuery {
        $this->whereNot(fn(BaseQuery $q) => $q->whereLike($column, $value, $case));

        return $this;
    }

    /**
     * Where column is like the given value.
     *
     * @param string $column
     * @param array $values
     * @param bool $case
     *
     * @return BaseQuery
     * @SuppressWarnings(PHPMD)
     */
    public function whereLikeAny(
        string $column,
        array $values,
        bool $case = false
    ): BaseQuery {
        $values = array_map(
            fn($val) => "'%$val%'",
            $values
        );

        $operator = $case ? 'like any' : 'ilike any';

        $sql = sprintf(
            '%s::text %s (array[%s])',
            $column,
            $operator,
            implode(', ', $values)
        );

        $this->whereRaw($sql);

        return $this;
    }

    /**
     * Where column is not like the given value.
     *
     * @param string $column
     * @param array $values
     * @param bool $case
     *
     * @return BaseQuery
     * @SuppressWarnings(PHPMD)
     */
    public function whereNotLikeAny(
        string $column,
        array $values,
        bool $case = false
    ): BaseQuery {
        $this->whereNot(fn(BaseQuery $q) => $q->whereLikeAny($column, $values, $case));

        return $this;
    }

    /**
     * Create a new query instance for wrapped where condition.
     *
     * @return static
     */
    public function forWrappedWhere(): static
    {
        /** @var static $builder */
        $builder = $this->model->newModelQuery();

        return $builder;
    }

    /**
     * Add a wrapped where statement to the query.
     *
     * @param Closure $callback
     * @param string $boolean
     *
     * @return static
     */
    public function whereWrapped(Closure $callback, string $boolean = 'and'): static
    {
        call_user_func($callback, $query = $this->forWrappedWhere());
        $this->addNestedWhereQuery($query->getQuery(), $boolean);

        return $this;
    }

    /**
     * Substitute a where column name with a different one.
     *
     * @param string $column
     * @param string $substitution
     *
     * @return static
     */
    public function substituteWhere(string $column, string $substitution): static
    {
        $substitute = function (array $wheres) use (&$substitute, $column, $substitution) {
            foreach ($wheres as &$where) {
                if ($where['type'] === 'Nested') {
                    // Recursively handle nested where clauses
                    $where['query']->wheres = $substitute($where['query']->wheres, $column, $substitution);
                } elseif (isset($where['column']) && $where['column'] === $column) {
                    $where['column'] = $substitution;
                }
            }
            return $wheres;
        };

        $this->getQuery()->wheres = $substitute($this->getQuery()->wheres);

        return $this;
    }

    /**
     * Get list of scopes that query has.
     *
     * @return array
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * Determines if query has a given scope.
     *
     * @param string $scope
     *
     * @return bool
     */
    public function hasScope(string $scope): bool
    {
        return in_array($scope, $this->getScopes())
            || array_key_exists($scope, $this->getScopes());
    }

    /**
     * Get list of joins that query has. Keys are aliases
     * and values are table names.
     *
     * @return array
     */
    public function getJoins(): array
    {
        $joins = [];

        // @phpstan-ignore-next-line
        foreach ($this->getQuery()->joins ?? [] as $join) {
            /** @var JoinClause $join */
            $table = $join->table;
            $alias = null;

            $matches = [];
            $pattern = '/(?<table>(\w+|\w+[.]\w+))(\W+as\W+)(?<alias>(\w+|\w+[.]\w+))/';

            if (preg_match($pattern, $table, $matches)) {
                $table = data_get($matches, 'table');
                $alias = data_get($matches, 'alias');
            }

            $joins[$alias ?? $table] = $table;
        }

        return $joins;
    }

    /**
     * Get alias of given joined table. Null is returned if given
     * table is not joined. If there are multiple joins of the
     * given table then only the first alias will be returned.
     *
     *
     * @param string $table
     *
     * @return string|null
     */
    public function getJoinAlias(string $table): ?string
    {
        return array_search($table, $this->getJoins());
    }

    /**
     * Determines if query has a given join by table name and optionally
     * check if it's joined using a given alias.
     *
     * @param string $table
     * @param string|null $alias
     *
     * @return bool
     */
    public function hasJoin(string $table, ?string $alias = null): bool
    {
        $joins = $this->getJoins();

        if (empty($alias)) {
            return in_array($table, $joins);
        }

        return in_array($table, $joins)
            && array_key_exists($alias, $joins);
    }

    /**
     * Chunk the results of the query, but respect the
     * limit and offset if they are present.
     *
     * @param callable $callback
     * @param int $count
     *
     * @return void
     */
    public function chunkPaginated(callable $callback, int $count = 1000): void
    {
        // makes sure that previously set limit and offset are followed
        $originalLimit = $this->getQuery()->limit ?? 0; // @phpstan-ignore-line
        $originalOffset = $this->getQuery()->offset ?? 0; // @phpstan-ignore-line

        $retrieved = 0;
        $step = min($originalLimit, $count);

        while ($retrieved !== $originalLimit) {
            $query = $this->clone()
                ->offset($originalOffset + $retrieved)
                ->limit($step);

            $callback(($records = $query->get()));
            $count = $records->count();
            $retrieved += $count;

            if ($count < $step) {
                break;
            }
        }
    }
}
