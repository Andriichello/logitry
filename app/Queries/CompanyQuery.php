<?php

namespace App\Queries;

use App\Models\Company;
use App\Queries\Interfaces\IndexableInterface;

/**
 * Class CompanyQuery.
 *
 * @property Company $model
 *
 * @method CompanyQuery select($columns = ['*'])
 * @method CompanyQuery whereKey($id)
 * @method Company|null find($id, $columns = ['*'])
 * @method Company findOrFail($id, $columns = ['*'])
 * @method Company|null first($columns = ['*'])
 * @method Company firstOrFail($columns = ['*'])
 * @method Company make(array $attributes = [])
 * @method Company create(array $attributes = [])
 * @method Company updateOrCreate(array $attributes, array $values = [])
 */
class CompanyQuery extends BaseQuery implements IndexableInterface
{
    //
}
