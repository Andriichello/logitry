<?php

namespace App\Queries;

use App\Models\Company;
use App\Models\Trip;
use App\Queries\Interfaces\IndexableInterface;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Class TripQuery.
 *
 * @property Trip $model
 *
 * @method TripQuery select($columns = ['*'])
 * @method TripQuery whereKey($id)
 * @method Trip|null find($id, $columns = ['*'])
 * @method Trip findOrFail($id, $columns = ['*'])
 * @method Trip|null first($columns = ['*'])
 * @method Trip firstOrFail($columns = ['*'])
 * @method Trip make(array $attributes = [])
 * @method Trip create(array $attributes = [])
 * @method Trip updateOrCreate(array $attributes, array $values = [])
 */
class TripQuery extends BaseQuery implements IndexableInterface
{
    /**
     * Filter to trips that belong to given companies.
     *
     * @param Company|int ...$companies
     *
     * @return $this
     */
    public function withinCompanies(Company|int ...$companies): static
    {
        $values = array_map(
            fn ($company) => is_numeric($company) ? $company : $company->id,
            $companies
        );

        $this->join('routes', 'trips.route_id', '=', 'routes.id')
            ->whereIn('routes.company_id', $values)
            ->select('trips.*');

        return $this;
    }

    /**
     * Filter to trips that depart after the given date.
     *
     * @param Carbon $date
     *
     * @return static
     */
    public function departsAfter(Carbon $date): static
    {
        $this->where('departs_at', '>=', $date);

        return $this;
    }

    /**
     * Filter to trips that depart after the given date.
     *
     * @param Carbon $date
     *
     * @return static
     */
    public function orDepartsAfter(Carbon $date): static
    {
        $this->orWhere('departs_at', '>=', $date);

        return $this;
    }

    /**
     * Filter to trips that depart before the given date.
     *
     * @param Carbon $date
     *
     * @return static
     */
    public function departsBefore(Carbon $date): static
    {
        $this->where('departs_at', '<=', $date);

        return $this;
    }

    /**
     * Filter to trips that depart before the given date.
     *
     * @param Carbon $date
     *
     * @return static
     */
    public function orDepartsBefore(Carbon $date): static
    {
        $this->orWhere('departs_at', '<=', $date);

        return $this;
    }

    /**
     * Filter to trips that depart within the given time interval.
     *
     * @param Carbon|null $beg
     * @param Carbon|null $end
     *
     * @return static
     */
    public function departsWithin(?Carbon $beg, ?Carbon $end): static
    {
        $this->where(function (TripQuery $query) use ($beg, $end) {
            if ($beg) {
                $query->departsAfter($beg);
            }

            if ($end) {
                $query->departsBefore($end);
            }
        });

        return $this;
    }

    /**
     * Filter to trips that depart within the given time interval.
     *
     * @param Carbon|null $beg
     * @param Carbon|null $end
     *
     * @return static
     */
    public function orDepartsWithin(?Carbon $beg, ?Carbon $end): static
    {
        $this->where(function (TripQuery $query) use ($beg, $end) {
            if ($beg) {
                $query->departsAfter($beg);
            }

            if ($end) {
                $query->departsBefore($end);
            }
        });

        return $this;
    }

    /**
     * Filter to trips that arrive after the given date.
     *
     * @param Carbon $date
     *
     * @return static
     */
    public function arrivesAfter(Carbon $date): static
    {
        $this->where('arrives_at', '>=', $date);

        return $this;
    }

    /**
     * Filter to trips that arrive after the given date.
     *
     * @param Carbon $date
     *
     * @return static
     */
    public function orArrivesAfter(Carbon $date): static
    {
        $this->orWhere('arrives_at', '>=', $date);

        return $this;
    }

    /**
     * Filter to trips that arrive before the given date.
     *
     * @param Carbon $date
     *
     * @return static
     */
    public function arrivesBefore(Carbon $date): static
    {
        $this->where('arrives_at', '<=', $date);

        return $this;
    }

    /**
     * Filter to trips that arrive before the given date.
     *
     * @param Carbon $date
     *
     * @return static
     */
    public function orArrivesBefore(Carbon $date): static
    {
        $this->orWhere('arrives_at', '<=', $date);

        return $this;
    }

    /**
     * Filter to trips that arrive within the given time interval.
     *
     * @param Carbon|null $beg
     * @param Carbon|null $end
     *
     * @return static
     */
    public function arrivesWithin(?Carbon $beg, ?Carbon $end): static
    {
        $this->where(function (TripQuery $query) use ($beg, $end) {
            if ($beg) {
                $query->arrivesAfter($beg);
            }

            if ($end) {
                $query->arrivesBefore($end);
            }
        });

        return $this;
    }

    /**
     * Filter to trips that arrive within the given time interval.
     *
     * @param Carbon|null $beg
     * @param Carbon|null $end
     *
     * @return static
     */
    public function orArrivesWithin(?Carbon $beg, ?Carbon $end): static
    {
        $this->orWhere(function (TripQuery $query) use ($beg, $end) {
            if ($beg) {
                $query->arrivesAfter($beg);
            }

            if ($end) {
                $query->arrivesBefore($end);
            }
        });

        return $this;
    }

    /**
     * Composes a raw select to get highlights of trips
     * (grouped by departure date).
     *
     * @return Builder
     */
    public function highlights(): Builder
    {
        $selects = [
            'DATE(trips.departs_at) as date',
            'COUNT(trips.id) as count',
            'GROUP_CONCAT(trips.id) as ids',
        ];

        return DB::table($this->getQuery(), 'trips')
            ->selectRaw(join(', ', $selects))
            ->groupBy('date')
            ->orderBy('date');
    }
}
