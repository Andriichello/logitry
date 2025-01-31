<?php

namespace App\Models;

use App\Models\Interfaces\IdentifiesCompanyInterface;
use App\Models\Traits\IdentifiesCompany;
use App\Queries\BaseQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as DatabaseBuilder;
use Illuminate\Support\Carbon;

/**
 * Class BaseModel.
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static BaseQuery query()
 */
class BaseModel extends Model implements
    IdentifiesCompanyInterface
{
    use IdentifiesCompany;

    /**
     * @param DatabaseBuilder $query
     *
     * @return BaseQuery
     */
    public function newEloquentBuilder($query): BaseQuery
    {
        return new BaseQuery($query);
    }
}
