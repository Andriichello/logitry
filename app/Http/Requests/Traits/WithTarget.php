<?php

namespace App\Http\Requests\Traits;

use App\Http\Requests\BaseRequest;
use App\Models\BaseModel;
use App\Queries\BaseQuery;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Trait WithTarget.
 *
 * @mixin BaseRequest
 */
trait WithTarget
{
    /**
     * Target id.
     *
     * @var mixed
     */
    protected mixed $id;

    /**
     * Last loaded target model.
     *
     * @var BaseModel|null
     */
    protected ?BaseModel $target;

    /**
     * Get target id.
     *
     * @return mixed
     */
    public function getId(): mixed
    {
        if (!isset($this->id)) {
            $this->id = $this->route('id');
        }

        if (!isset($this->id)) {
            $this->id = $this->get('id');
        }

        return $this->id ?? null;
    }

    /**
     * Set target id.
     *
     * @param mixed $value
     *
     * @return static
     */
    public function setId(mixed $value): static
    {
        $this->id = $value;

        return $this;
    }

    /**
     * Get query for finding the target model.
     *
     * @param BaseModel|string $model
     *
     * @return BaseQuery
     */
    public function targetQuery(BaseModel|string $model): BaseQuery
    {
        return $model::query()
            ->whereKey($this->getId())
            ->index($this);
    }

    /**
     * Get target model.
     *
     * @param BaseModel|string $model
     *
     * @return BaseModel|null
     */
    public function target(BaseModel|string $model): ?BaseModel
    {
        if (isset($this->target) && is_a($this->target, $model)) {
            return $this->target;
        }

        return $this->target = $this->targetQuery($model)->first();
    }

    /**
     * Get target model or fail.
     *
     * @param BaseModel|string $model
     *
     * @return BaseModel
     * @throws ModelNotFoundException
     */
    public function targetOrFail(BaseModel|string $model): BaseModel
    {
        if (isset($this->target) && is_a($this->target, $model)) {
            return $this->target;
        }

        return $this->target = $this->targetQuery($model)->firstOrFail();
    }
}
