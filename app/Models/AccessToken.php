<?php

namespace App\Models;

use App\Enum\Role;
use App\Models\Interfaces\IdentifiesCompanyInterface;
use App\Models\Traits\IdentifiesCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Class AccessToken.
 *
 * @property int $id
 * @property int|null $tokenable_id
 * @property string|null $tokenable_type
 * @property string|null $name
 * @property string|null $token
 * @property string[]|null $abilities
 * @property object|null $metadata
 * @property Carbon|null $last_used_at
 * @property Carbon|null $expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property int|null $company_id
 * @property Role|null $role
 *
 * @property User|BaseModel $tokenable
 * @property Company|null $company
 */
class AccessToken extends PersonalAccessToken implements
    IdentifiesCompanyInterface
{
    use IdentifiesCompany;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'personal_access_tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'role',
        'name',
        'token',
        'abilities',
        'expires_at',
        'metadata',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'role' => Role::class,
        'abilities' => 'json',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'object',
    ];

    /**
     * The loadable relationships for the model.
     *
     * @var array
     */
    protected array $relationships = [
        'company',
    ];

    /**
     * Company associated with the model.
     *
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
