<?php

namespace App\Models;

use App\Enum\Role;
use App\Models\Interfaces\IdentifiesCompanyInterface;
use App\Queries\BaseQuery;
use Database\Factories\UserFactory;
use DateTimeInterface;
use Exception;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;
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
 *
 * @property int|null $company_id Dynamic (not saved)
 *
 * @property-read Company|null $company
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
    use HasApiTokens {
        createToken as baseCreateToken;
    }

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
     * Company associated with the model.
     * Important: this is a dynamic read-only relationship,
     * which is being resolved using `companyId()`.
     *
     * @return HasOneThrough
     */
    public function company(): HasOneThrough
    {
        return $this->hasOneThrough(
            Company::class,
            CompanyUser::class,
            'user_id',
            'id',
            'id',
            'company_id'
        );
    }

    /**
     * Companies associated with the model.
     *
     * @return BelongsToMany
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(
            Company::class,
            'company_user',
            'user_id',
            'company_id'
        )
            ->withPivot(['role', 'deactivated_at']);
    }

    /**
     * Sub-companies associated with the model.
     *
     * @return BaseQuery
     */
    public function subCompanies(): BaseQuery
    {
        $parentCompanies = $this->companies()
            ->select('companies.id');

        return Company::query()
            ->whereIn('parent_id', $parentCompanies);
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
     * Checks if user is a part of given company.
     *
     * @param Company|int|null $company
     *
     * @return bool
     */
    public function isPartOf(Company|int|null $company): bool
    {
        $id = $company instanceof Company
            ? $company->id : $company;

        return in_array($id, $this->companyIds());
    }

    /**
     * Checks if user has given role in given company.
     *
     * @param Role|string $role
     * @param Company|int $companyId
     *
     * @return bool
     */
    public function isOfRole(Role|string $role, Company|int $companyId): bool
    {
        if ($role instanceof Role) {
            $role = $role->value;
        }

        if ($companyId instanceof Company) {
            $companyId = $companyId->id;
        }

        $companyRoles = $this->companyRoles();
        $companyRole = data_get($companyRoles, $companyId);

        if ($companyRole instanceof Role) {
            $companyRole = $companyRole->value;
        }

        return $role === $companyRole;
    }

    /**
     * Accessor for `company_id` attribute.
     *
     * @return int|null
     */
    public function getCompanyIdAttribute(): ?int
    {
        if (!array_key_exists('company_id', $this->attributes)) {
            $this->attributes['company_id'] = $this->companyId();
        }

        return $this->attributes['company_id'];
    }

    /**
     * Mutator for `company_id` attribute.
     *
     * @param int|null $companyId
     *
     * @return void
     */
    public function setCompanyIdAttribute(?int $companyId): void
    {
        $this->attributes['company_id'] = $companyId;
    }

    /**
     * Company id associated with the model.
     *
     * @return int|null
     */
    public function companyId(): ?int
    {
        try {
            /** @var User|null $user */
            $user = request()->user();

            if ($user && $user->id === $this->id) {
                $companyId = session('auth.company_id');

                if (!$companyId) {
                    $token = $user->currentAccessToken();

                    if ($token instanceof IdentifiesCompanyInterface) {
                        $companyId = $token->companyId();
                    }
                }
            }
        } catch (Throwable) {
            //
        }

        if (empty($companyId) && !empty($this->attributes['company_id'])) {
            $companyId = $this->attributes['company_id'];
        }

        if (empty($companyId)) {
            /** @var Company|null $parentCompany */
            $parentCompany = $this->companies
                ->whereNull('parent_id')
                ->first();

            if ($parentCompany) {
                return $parentCompany->id;
            }

            return data_get($this->companies->first(), 'id');
        }

        /** @var Company|null $current */
        $current = $this->companies
            ->where('id', $companyId)
            ->first();

        if ($current) {
            return $current->id;
        }

        /** @var Company|null $current */
        $current = $this->subCompanies()
            ->where('id', $companyId)
            ->first();

        return $current?->id;
    }

    /**
     * Company ids associated with the model.
     *
     * @return int[]
     */
    public function companyIds(): array
    {
        $ids = [];

        foreach ($this->companies as $company) {
            $ids[] = $company->id;
        }

        if (empty($ids)) {
            return $ids;
        }

        $this->subCompanies()
            ->each(function (Company $company) use (&$ids) {
                $ids[] = $company->id;
            });

        return array_values(array_unique(array_filter($ids)));
    }

    /**
     * Company ids to corresponding user roles associated with the model.
     *
     * @return array<int, Role>
     */
    public function companyRoles(): array
    {
        $roles = [];

        foreach ($this->companies as $company) {
            $role = data_get($company, 'pivot.role');
            $role = Role::tryFrom($role) ?? $role;

            $roles[$company->id] = $role;
        }

        $this->subCompanies()
            ->each(function (Company $company) use (&$roles) {
                $roles[$company->id] = $roles[$company->parent_id] ?? null;
            });

        return array_filter($roles);
    }

    /**
     * Role of current user in given company.
     *
     * @param Company|int $companyId
     *
     * @return Role|string|null
     */
    public function roleInCompany(Company|int $companyId): Role|string|null
    {
        if ($companyId instanceof Company) {
            $companyId = $companyId->id;
        }

        return data_get($this->companyRoles(), $companyId);
    }

    /**
     * Create a new personal access token for the user.
     *
     * @param string $name
     * @param array $abilities
     * @param DateTimeInterface|null $expiresAt
     *
     * @return NewAccessToken
     */
    public function createToken(
        string $name,
        array $abilities = ['*'],
        DateTimeInterface $expiresAt = null
    ): NewAccessToken {
        if (empty($expiresAt)) {
            $minutes = config('sanctum.expiration');

            if ($minutes && $minutes > 0) {
                $expiresAt = now()->addMinutes($minutes);
            }
        }

        return $this->baseCreateToken($name, $abilities, $expiresAt);
    }
}
