<?php

namespace App\Models;

use Database\Factories\PointFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Point.
 *
 * @property int $route_id
 * @property int $number
 * @property string $name
 * @property string|null $description
 * @property string|null $country
 * @property string|null $city
 * @property string|null $street
 * @property float $latitude
 * @property float $longitude
 * @property int|null $travel_time
 * @property int|null $travel_time_cap
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Company $route
 *
 * @method static PointFactory factory(...$parameters)
 */
class Point extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'route_id',
        'number',
        'name',
        'description',
        'country',
        'city',
        'street',
        'latitude',
        'longitude',
        'travel_time',
        'travel_time_cap',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
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
}
