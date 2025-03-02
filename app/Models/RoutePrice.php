<?php

namespace App\Models;

use App\Enum\SaleUnit;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Route.
 *
 * @property int $route_id
 * @property int|null $beg_point_id
 * @property int|null $end_point_id
 * @property string|null $name
 * @property string|null $description
 * @property string $unit
 * @property float $from
 * @property float|null $to
 * @property string $currency
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property int|null $travel_time
 * @property int|null $travel_time_cap
 *
 * @property Route $route
 * @property Point|null $begPoint
 * @property Point|null $endPoint
 */
class RoutePrice extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'route_id',
        'beg_point_id',
        'end_point_id',
        'name',
        'description',
        'unit',
        'from',
        'to',
        'currency',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'unit' => SaleUnit::class,
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
