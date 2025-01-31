<?php

namespace App\Http\Requests\Crud;

use App\Http\Requests\CrudRequest;
use App\Http\Requests\Interfaces\WithTargetInterface;
use App\Http\Requests\Traits\WithTarget;

/**
 * Class UpdateRequest.
 */
class UpdateRequest extends CrudRequest implements WithTargetInterface
{
    use WithTarget;

    /**
     * Ability, which should be checked on the request.
     *
     * @var string|null
     */
    protected ?string $ability = 'update';
}
