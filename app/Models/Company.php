<?php

namespace App\Models;

use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Class Company.
 *
 * @property int|null $parent_id
 * @property string $name
 * @property string|null $realm
 * @property string|null $plan
 * @property object|null $metadata
 * @property Carbon|null $deactivated_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Company|null $parent
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
     * Users associated with the model.
     *
     * @return HasManyThrough
     */
    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, CompanyUser::class);
    }

    /**
     * Get the corresponding company id.
     *
     * @return int|null
     */
    public function companyId(): ?int
    {
        return $this->id;
    }
}
