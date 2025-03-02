<?php

namespace App\Queries;

use App\Models\Route;
use App\Queries\Interfaces\IndexableInterface;
use Carbon\Carbon;

/**
 * Class RouteQuery.
 *
 * @property Route $model
 *
 * @method RouteQuery select($columns = ['*'])
 * @method RouteQuery whereKey($id)
 * @method Route|null find($id, $columns = ['*'])
 * @method Route findOrFail($id, $columns = ['*'])
 * @method Route|null first($columns = ['*'])
 * @method Route firstOrFail($columns = ['*'])
 * @method Route make(array $attributes = [])
 * @method Route create(array $attributes = [])
 * @method Route updateOrCreate(array $attributes, array $values = [])
 */
class RouteQuery extends BaseQuery implements IndexableInterface
{
    /**
     * Filter to routes that go through the given countries.
     *
     * @param string ...$countries alpha2 strings
     *
     * @return $this
     */
    public function withinCountries(string ...$countries): static
    {
        $this->whereHas('points', function ($query) use ($countries) {
            $query->whereIn('country', $countries);
        });

        return $this;
    }

    /**
     * Filter to routes with trips that depart within the given time interval.
     *
     * @param Carbon|null $beg
     * @param Carbon|null $end
     *
     * @return static
     */
    public function tripsDepartWithin(?Carbon $beg, ?Carbon $end): static
    {
        $this->whereHas('trips', function (TripQuery $query) use ($beg, $end) {
            $query->departsWithin($beg, $end);
        });

        return $this;
    }

    /**
     * Filter to routes with trips that depart within the given time interval.
     *
     * @param Carbon|null $beg
     * @param Carbon|null $end
     *
     * @return static
     */
    public function orTripsDepartWithin(?Carbon $beg, ?Carbon $end): static
    {
        $this->orWhereHas('trips', function (TripQuery $query) use ($beg, $end) {
            $query->departsWithin($beg, $end);
        });

        return $this;
    }

    /**
     * Filter to routes with trips that arrive within the given time interval.
     *
     * @param Carbon|null $beg
     * @param Carbon|null $end
     *
     * @return static
     */
    public function tripsArriveWithin(?Carbon $beg, ?Carbon $end): static
    {
        $this->whereHas('trips', function (TripQuery $query) use ($beg, $end) {
            $query->arrivesWithin($beg, $end);
        });

        return $this;
    }

    /**
     * Filter to routes with trips that arrive within the given time interval.
     *
     * @param Carbon|null $beg
     * @param Carbon|null $end
     *
     * @return static
     */
    public function orTripsArriveWithin(?Carbon $beg, ?Carbon $end): static
    {
        $this->orWhereHas('trips', function (TripQuery $query) use ($beg, $end) {
            $query->arrivesWithin($beg, $end);
        });

        return $this;
    }
}
