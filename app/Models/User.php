<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Throwable;

/**
 * Class User.
 *
 * @property string $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $password
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $phone_verified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Company[]|Collection $companies
 *
 * @method static UserFactory factory(...$parameters)
 */
class User extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract
{
    use HasFactory;
    use Authorizable;
    use Authenticatable;

    /**
     * The table associated with the model.
     *
     * @var string|null
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    /**
     * Companies associated with the model.
     *
     * @return HasManyThrough
     */
    public function companies(): HasManyThrough
    {
        return $this->hasManyThrough(Company::class, CompanyUser::class);
    }

    /**
     * Checks if user is verified.
     *
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->email_verified_at || $this->phone_verified_at;
    }

    /**
     * Get the corresponding company id.
     *
     * @return int|null
     */
    public function companyId(): ?int
    {
        try {
            $companyId = session('auth.company_id');

            if (empty($companyId)) {
                return $companyId;
            }
        } catch (Throwable) {
            //
        }

        return data_get($this->companies->first(), 'id');
    }
}
