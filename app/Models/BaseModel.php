<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class BaseModel.
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class BaseModel extends Model
{
    /**
     * Get the corresponding company id.
     *
     * @return int|null
     */
    public function companyId(): ?int
    {
        return data_get($this, 'company_id');
    }
}
