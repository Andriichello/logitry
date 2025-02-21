<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
 */
class Route extends BaseModel
{
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
     * @return BelongsTo
     */
    public function points(): BelongsTo
    {
        return $this->belongsTo(Point::class);
    }
}
