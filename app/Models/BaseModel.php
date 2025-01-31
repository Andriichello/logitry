<?php

namespace App\Models;

use App\Models\Interfaces\IdentifiesCompanyInterface;
use App\Models\Traits\IdentifiesCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class BaseModel.
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class BaseModel extends Model implements
    IdentifiesCompanyInterface
{
    use IdentifiesCompany;
}
