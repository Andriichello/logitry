<?php

namespace App\Policies;

use App\Http\Requests\CrudRequest;
use App\Http\Requests\Interfaces\WithTargetInterface;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Http\Response;

/**
 * Class CrudPolicy.
 */
class CrudPolicy extends BasePolicy
{
    /**
     * Determine if user is allowed to perform given crud request.
     *
     * @param CrudRequest $request
     *
     * @return Response|bool
     */
    public function determine(CrudRequest $request): Response|bool
    {
        $ability = $request->getAbility();
        if (!$ability) {
            return true;
        }

        $user = $request->user();
        $before = $this->before($user, $ability);
        if ($before !== null) {
            return $before;
        }

        return $request instanceof WithTargetInterface
            ? $this->$ability($user, $request->targetOrFail($this->getModel()))
            : $this->$ability($user);
    }

    /**
     * Perform pre-authorization checks. Be aware that the ability
     * method won't be called unless null is returned here.
     *
     * @param User|null $user
     * @param string $ability
     *
     * @return Response|bool|null
     * @SuppressWarnings(PHPMD)
     */
    public function before(mixed $user, string $ability): Response|bool|null
    {
        if (!$user && !in_array($ability, ['view', 'viewAny'])) {
            return false;
        }

        return parent::before($user, $ability);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User|null $user
     * @param BaseModel $model
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(mixed $user, mixed $model): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param User|null $user
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function viewAny(mixed $user): bool
    {
        return true;
    }
}
