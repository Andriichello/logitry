<?php

namespace App\Models;

use App\Enum\TripStatus;
use App\Queries\TripQuery;
use Database\Factories\TripFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder as DatabaseBuilder;
use Illuminate\Support\Carbon;

/**
 * Class Trip.
 *
 * @property int $route_id
 * @property bool $reversed
 * @property int|null $vehicle_id
 * @property int|null $driver_id
 * @property int|null $contact_id
 * @property TripStatus $status
 * @property object|null $metadata
 * @property Carbon|null $departs_at
 * @property Carbon|null $arrives_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Route $route
 * @property Vehicle|null $vehicle
 * @property User|null $driver
 * @property Contact|null $contact
 *
 * @method static TripQuery query()
 * @method static TripFactory factory(...$parameters)
 */
class Trip extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'route_id',
        'reversed',
        'vehicle_id',
        'driver_id',
        'contact_id',
        'status',
        'departs_at',
        'arrives_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'reversed' => 'bool',
        'status' => TripStatus::class,
        'metadata' => 'object',
        'departs_at' => 'datetime',
        'arrives_at' => 'datetime',
    ];

    /**
     * Route associated with the model.
     *
     * @return BelongsTo
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
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
     * Contact associated with the model.
     *
     * @return BelongsTo
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * @param DatabaseBuilder $query
     *
     * @return TripQuery
     */
    public function newEloquentBuilder($query): TripQuery
    {
        return new TripQuery($query);
    }
}
