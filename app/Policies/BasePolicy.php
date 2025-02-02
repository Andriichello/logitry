<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Http\Response;
use App\Models\BaseModel;

/**
 * Class BasePolicy.
 *
 * @author Andrii Prykhodko <andriichello@gmail.com>
 * @package App\Policies
 */
abstract class BasePolicy
{
    use HandlesAuthorization;

    /**
     * Model, which is controlled by the policy.
     *
     * @var BaseModel|string
     */
    protected BaseModel|string $model;

    /**
     * Get model, which is controlled by the policy.
     *
     * @return BaseModel|string|null
     */
    public function getModel(): BaseModel|string|null
    {
        return $this->model ?? null;
    }

    /**
     * Perform pre-authorization checks. Be aware that the ability
     * method won't be called unless null is returned here.
     *
     * @param mixed $user
     * @param string $ability
     *
     * @return Response|bool|null
     * @SuppressWarnings(PHPMD)
     */
    public function before(mixed $user, string $ability): Response|bool|null
    {
        return null;
    }

    /**
     * Determine if user is allowed to perform request
     * when the method is missing.
     *
     * @param string $name
     * @param array $arguments
     *
     * @return bool
     */
    public function __call(string $name, array $arguments): bool
    {
        return $this->determineMissing($name, ...$arguments);
    }

    /**
     * Determine if ability should be authorized
     * if method with corresponding name is missing.
     *
     * @param string $ability
     * @param mixed ...$args
     *
     * @return bool
     * @SuppressWarnings(PHPMD)
     */
    public function determineMissing(string $ability, mixed ...$args): bool
    {
        return true;
    }
}
