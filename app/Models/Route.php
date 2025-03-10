<?php

namespace App\Models;

use App\Queries\RouteQuery;
use Database\Factories\RouteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Builder as DatabaseBuilder;
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
 * @property int|null $travel_time
 * @property int|null $travel_time_cap
 *
 * @property Company $company
 * @property Contact|null $contact
 * @property Vehicle|null $vehicle
 * @property User|null $driver
 * @property Point[]|Collection $points
 * @property RoutePrice[]|Collection $prices
 * @property-read RoutePrice[]|Collection $relevantPrices
 * @property Trip[]|Collection $trips
 *
 * @method static RouteQuery query()
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
        /** @phpstan-ignore-next-line */
        return $this->hasMany(Point::class)
            ->orderBy('number');
    }

    /**
     * Prices associated with the model.
     *
     * @return HasMany
     */
    public function prices(): HasMany
    {
        return $this->hasMany(RoutePrice::class);
    }

    /**
     * Relevant prices associated with the model.
     *
     * @return HasMany
     */
    public function relevantPrices(): HasMany
    {
        return $this->prices()
            ->whereIn('id', function (Builder $query) {
                $query->selectRaw('MAX(id)')
                    ->from('route_prices')
                    ->where('route_id', $this->id)
                    ->groupBy('unit');
            });
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

    /**
     * Accessor for the `travel_time` attribute.
     *
     * @return int|null
     */
    public function getTravelTimeAttribute(): ?int
    {
        if (empty($this->points)) {
            return null;
        }

        $travelTime = 0;

        foreach ($this->points as $index => $point) {
            if ($index === 0) {
                continue;
            }

            if ($point->travel_time === null) {
                return null;
            }

            $travelTime += $point->travel_time;
        }

        return $travelTime;
    }

    /**
     * Accessor for the `travel_time_cap` attribute.
     *
     * @return int|null
     */
    public function getTravelTimeCapAttribute(): ?int
    {
        if (empty($this->points)) {
            return null;
        }

        $travelTimeCap = 0;

        foreach ($this->points as $index => $point) {
            if ($index === 0) {
                continue;
            }

            if ($point->travel_time_cap === null) {
                return null;
            }

            $travelTimeCap += $point->travel_time_cap;
        }

        return $travelTimeCap;
    }

    /**
     * @param DatabaseBuilder $query
     *
     * @return RouteQuery
     */
    public function newEloquentBuilder($query): RouteQuery
    {
        return new RouteQuery($query);
    }
}
