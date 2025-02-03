<?php

namespace App\Models;

use App\Enum\Realm;
use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Class Company.
 *
 * @property int|null $parent_id
 * @property string $abbreviation
 * @property string $name
 * @property Realm|null $realm
 * @property string|null $plan
 * @property object|null $metadata
 * @property Carbon|null $deactivated_at
 *
 * @property Company|null $parent
 * @property Company[]|Collection $subCompanies
 * @property User[]|Collection $users
 *
 * @method static CompanyFactory factory(...$parameters)
 */
class Company extends BaseModel
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string|null
     */
    protected $table = 'companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'parent_id',
        'abbreviation',
        'name',
        'realm',
        'plan',
        'metadata',
        'deactivated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'realm' => Realm::class,
        'metadata' => 'object',
        'deactivated_at' => 'datetime',
    ];

    /**
     * Parent company associated with the model.
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'parent_id');
    }

    /**
     * Sub-companies associated with the model.
     *
     * @return HasMany
     */
    public function subCompanies(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    /**
     * Users associated with the model.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'company_user',
            'company_id',
            'user_id'
        )
            ->withPivot(['role', 'deactivated_at']);
    }

    /**
     * Company id associated with the model.
     *
     * @return int|null
     */
    public function companyId(): ?int
    {
        return $this->id;
    }

    /**
     * Finds company by given `id`, `abbreviation` or `name`.
     *
     * @param int|null $id
     * @param string|null $abb abbreviation
     * @param string|null $name
     *
     * @return BaseModel|null
     */
    public static function findBy(
        ?int $id = null,
        ?string $abb = null,
        ?string $name = null
    ): ?BaseModel {
        if ($id !== null) {
            return static::query()
                ->where('id', $id)
                ->first();
        }

        if ($abb !== null) {
            return static::query()
                ->where('abbreviation', strtolower($abb))
                ->first();
        }

        if ($name !== null) {
            return static::query()
                ->where('name', $name)
                ->first();
        }

        return null;
    }
}
