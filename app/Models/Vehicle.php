<?php

namespace App\Models;

use App\Enum\Fuel;
use App\Enum\VehicleType;
use Database\Factories\VehicleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Vehicle.
 *
 * @property int $company_id
 * @property string $manufacturer
 * @property string $model
 * @property int $year
 * @property string $color
 * @property string|null $nickname
 * @property string|null $description
 * @property VehicleType|null $type
 * @property Fuel|null $fuel
 * @property int|null $seats
 * @property float|null $cargo_width
 * @property float|null $cargo_length
 * @property float|null $cargo_height
 * @property float|null $cargo_volume
 * @property int|null $cargo_capacity
 * @property object|null $metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Company $company
 *
 * @method static VehicleFactory factory(...$parameters)
 */
class Vehicle extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'manufacturer',
        'model',
        'year',
        'color',
        'nickname',
        'description',
        'type',
        'fuel',
        'seats',
        'cargo_width',
        'cargo_length',
        'cargo_height',
        'cargo_volume',
        'cargo_capacity',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'type' => VehicleType::class,
        'fuel' => Fuel::class,
        'cargo_width' => 'float',
        'cargo_length' => 'float',
        'cargo_height' => 'float',
        'cargo_volume' => 'float',
        'metadata' => 'object',
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
}
