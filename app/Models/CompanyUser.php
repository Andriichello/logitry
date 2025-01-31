<?php

namespace App\Models;

use App\Enum\Role;
use Database\Factories\CompanyUserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * Class CompanyUser.
 *
 * @property int $company_id
 * @property int $user_id
 * @property Role $role
 * @property Carbon|null $deactivated_at
 *
 * @property Company $company
 * @property User $user
 *
 * @method static CompanyUserFactory factory(...$parameters)
 */
class CompanyUser extends BaseModel
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string|null
     */
    protected $table = 'company_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'user_id',
        'role',
        'deactivated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'role' => Role::class,
        'deactivated_at' => 'datetime',
    ];
}
