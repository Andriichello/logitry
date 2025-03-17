<?php

namespace App\Http\Requests\Web;

use App\Http\Requests\BaseRequest;
use App\Http\Resources\Specific\RouteResource;
use App\Http\Resources\Specific\TripResource;
use App\Models\Company;
use App\Models\Route;
use App\Models\Trip;
use App\Queries\RouteQuery;
use App\Queries\TripQuery;
use Carbon\Carbon;

/**
 * Class MapRequest.
 *
 * @author Andrii Prykhodko <andriichello@gmail.com>
 * @package App\Http\Requests\Web
 */
class MapRequest extends BaseRequest
{
    /**
     * Last loaded company.
     *
     * @var Company|null
     */
    protected ?Company $company;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'beg' => [
                'sometimes',
                'nullable',
                'string',
                'date',
            ],
            'end' => [
                'sometimes',
                'nullable',
                'string',
                'date',
            ],
            'from' => [
                'sometimes',
                'nullable',
                'string',
            ],
            'to' => [
                'sometimes',
                'nullable',
                'string',
            ],
            'route' => [
                'sometimes',
                'nullable',
                'integer',
            ],
            'trip' => [
                'sometimes',
                'nullable',
                'integer',
            ],
            'has_trips' => [
                'sometimes',
                'nullable',
            ]
        ];
    }

    /**
     * Get the beginning of the time interval
     * to return trips for.
     *
     * @return Carbon|null
     */
    public function beg(): ?Carbon
    {
        $beg = $this->get('beg');

        if (empty($beg)) {
            return null;
        }

        $date = Carbon::parse($beg);

        if (!str_contains($beg, ':')) {
            $date->setTime(0, 0);
        }

        return $date;
    }

    /**
     * Get the end of the time interval
     * to return trips for.
     *
     * @return Carbon|null
     */
    public function end(): ?Carbon
    {
        $end = $this->get('end');

        if (empty($end)) {
            return null;
        }

        $date = Carbon::parse($end);

        if (!str_contains($end, ':')) {
            $date->setTime(23, 59, 59);
        }

        return $date;
    }

    /**
     * Get the abbreviation of the company.
     *
     * @return string|null
     */
    public function abbreviation(): ?string
    {
        return $this->route('abbreviation');
    }

    /**
     * Get the company, which is viewed on the map.
     *
     * @return Company|null
     */
    public function company(): ?Company
    {
        if (empty($this->company)) {
            $abbreviation = $this->abbreviation();

            if (!empty($abbreviation)) {
                return $this->company = Company::findBy(abb: $abbreviation);
            }
        }

        return $this->company ?? null;
    }

    /**
     * Returns a query for routes to show on map.
     *
     * @return RouteQuery
     */
    public function routes(): RouteQuery
    {
        $query = Route::query();

        if ($this->boolean('has_trips')) {
            $today = now()->setTime(0, 0, 0, 1);

            $query->tripsArriveWithin($today, null);

            $beg = $this->beg();
            $end = $this->end();

            if ($beg || $end) {
                $query = Route::query()
                    ->tripsDepartWithin($beg, $end);
            }
        }

        $company = $this->company();

        if ($company) {
            $query->where('company_id', $company->id);
        }

        $from = $this->get('from');
        $from = explode(',', $from);
        $from = array_values(array_filter($from));

        $to = $this->get('to');
        $to = explode(',', $to);
        $to = array_values(array_filter($to));

        $countries = [];

        foreach ($from as $country) {
            $country = trim($country);

            if (!empty($country)) {
                $countries[] = $country;
            }
        }

        foreach ($to as $country) {
            $country = trim($country);

            if (!empty($country)) {
                $countries[] = $country;
            }
        }

        if (count($countries) > 0) {
            $query->withinCountries(...$countries);
        }

        return $query;
    }

    /**
     * Returns a query for all trips that are available starting from yesterday.
     *
     * @return TripQuery
     */
    public function tripsForHighlights(): TripQuery
    {
        $today = now()->subDay()
            ->setTime(0, 0, 0, 1);

        $query = Trip::query()
            ->departsAfter($today);

        $company = $this->company();

        if ($company) {
            $query->withinCompanies($company);
        }

        $from = $this->get('from');
        $from = explode(',', $from);
        $from = array_values(array_filter($from));

        $to = $this->get('to');
        $to = explode(',', $to);
        $to = array_values(array_filter($to));

        $countries = [];

        foreach ($from as $country) {
            $country = trim($country);

            if (!empty($country)) {
                $countries[] = $country;
            }
        }

        foreach ($to as $country) {
            $country = trim($country);

            if (!empty($country)) {
                $countries[] = $country;
            }
        }

        if (count($countries) > 0) {
            $query->withinCountries(...$countries);
        }

        return $query;
    }

    /**
     * Returns a query for trips to show on map.
     *
     * @return TripQuery
     */
    public function trips(): TripQuery
    {
        $today = now()->setTime(0, 0, 0, 1);

        $query = Trip::query()
            ->arrivesWithin($today, null);

        $beg = $this->beg();
        $end = $this->end();

        if ($beg || $end) {
            $query = Trip::query()
                ->departsWithin($beg, $end);
        }

        $company = $this->company();

        if ($company) {
            $query->withinCompanies($company);
        }

        $from = $this->get('from');
        $from = explode(',', $from);
        $from = array_values(array_filter($from));

        $to = $this->get('to');
        $to = explode(',', $to);
        $to = array_values(array_filter($to));

        $countries = [];

        foreach ($from as $country) {
            $country = trim($country);

            if (!empty($country)) {
                $countries[] = $country;
            }
        }

        foreach ($to as $country) {
            $country = trim($country);

            if (!empty($country)) {
                $countries[] = $country;
            }
        }

        if (count($countries) > 0) {
            $query->withinCountries(...$countries);
        }

        $query->orderBy('departs_at');

        return $query;
    }

    /**
     * Returns an array of filters that are applied on the map.
     *
     * @return array{
     *     abbreviation: string|null,
     *     beg: Carbon|null,
     *     end: Carbon|null,
     *     from: string|null,
     *     to: string|null,
     * }
     */
    public function filters(): array
    {
        return [
            'abbreviation' => $this->abbreviation(),
            'beg' => $this->beg(),
            'end' => $this->get('end'),
            'from' => $this->get('from'),
            'to' => $this->get('to'),
            'route' => $this->get('route'),
            'trip' => $this->get('trip'),
            'has_trips' => $this->boolean('has_trips'),
        ];
    }

    /**
     * Returns an array of selections that are applied on the map.
     *
     * @return array{
     *     route: RouteResource|null,
     *     trip: TripResource|null,
     * }
     */
    public function selections(): array
    {
        $selections = [
            'route' => null,
            'trip' => null,
        ];

        $company = $this->company();

        $routeId = $this->get('route');

        if (!empty($routeId)) {
            $route = Route::query()
                ->where('company_id', $company?->id)
                ->find($routeId);

            $selections['route'] = $route ? new RouteResource($route) : null;
        }

        $tripId = $this->get('trip');

        if (!empty($tripId)) {
            $trip = Trip::query()
                ->withinCompanies($company?->id)
                ->find($tripId);

            $selections['trip'] = $trip ? new TripResource($trip) : null;
        }

        return $selections;
    }
}
