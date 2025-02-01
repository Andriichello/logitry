<?php

namespace App\Http\Resources\User;

use App\Http\Resources\BaseCollection;

/**
 * Class UserCollection.
 */
class UserCollection extends BaseCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = UserResource::class;
}
