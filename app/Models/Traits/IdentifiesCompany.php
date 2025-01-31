<?php

namespace App\Models\Traits;

use App\Enum\Role;
use App\Models\BaseModel;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\PersonalAccessToken;
use Throwable;

/**
 * Trait IdentifiesCompany.
 *
 * @mixin BaseModel
 */
trait IdentifiesCompany
{
    /**
     * Company id associated with the model.
     *
     * @return int|null
     */
    public function companyId(): ?int
    {
        return data_get($this, 'company_id');
    }
}
