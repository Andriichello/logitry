<?php

namespace App\Models;

use Database\Factories\RouteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Class Route.
 *
 * @property int $company_id
 * @property int|null $contact_id
 * @property int|null $vehicle_id
 * @property int|null $driver_id
 * @property string|null $name
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Company $company
 * @property Contact|null $contact
 * @property Vehicle|null $vehicle
 * @property User|null $driver
 * @property Point[]|Collection $points
 * @property Trip[]|Collection $trips
 *
 * @method static RouteFactory factory(...$parameters)
 */
class Route extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'contact_id',
        'vehicle_id',
        'driver_id',
        'name',
        'description',
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

    /**
     * Contact associated with the model.
     *
     * @return BelongsTo
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Vehicle associated with the model.
     *
     * @return BelongsTo
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Driver associated with the model.
     *
     * @return BelongsTo
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Points associated with the model.
     *
     * @return HasMany
     */
    public function points(): HasMany
    {
        return $this->hasMany(Point::class)
            ->orderBy('number');
    }

    /**
     * Trips associated with the model.
     *
     * @return HasMany
     */
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }
}
