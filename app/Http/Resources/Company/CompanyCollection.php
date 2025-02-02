<?php

namespace App\Http\Resources\Company;

use App\Http\Resources\BaseCollection;

/**
 * Class CompanyCollection.
 */
class CompanyCollection extends BaseCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = CompanyResource::class;
}
