<?php

namespace Speedgoat\Skeleton\Http\Resources\Api;

use Speedgoat\Skeleton\Http\Resources\BaseCollection;

/**
 * Class UserCollection.
 *
 * @author Andrii Prykhodko <andriichello@gmail.com>
 * @package Speedgoat\Skeleton\Http\Resources\Api
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
