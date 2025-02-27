<?php

namespace App\Http\Requests\Web;

use App\Http\Requests\BaseRequest;
use App\Models\Company;
use App\Models\Route;
use App\Models\Trip;
use App\Models\User;
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
            'abbreviation' => [
                'sometimes',
                'nullable',
                'string',
            ],
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
     * Get the company, which is viewed on the map.
     *
     * @return Company|null
     */
    public function company(): ?Company
    {
        if (empty($this->company)) {
            $user = $this->user();

            if ($user instanceof User) {
                if ($user->company) {
                    return $this->company = $user->company;
                }
            }

            $abbreviation = $this->get('abbreviation');

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
        $today = now()->setTime(0, 0, 0, 1);

        $query = Route::query()
            ->tripsArriveWithin($today, null);

        $beg = $this->beg();
        $end = $this->end();

        if ($beg || $end) {
            $query = Route::query()
                ->tripsDepartWithin($beg, $end);
        }

        $company = $this->company();

        if ($company) {
            $query->where('company_id', $company->id);
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
            'abbreviation' => $this->get('abbreviation'),
            'beg' => $this->beg(),
            'end' => $this->get('end'),
            'from' => $this->get('from'),
            'to' => $this->get('to'),
        ];
    }
}
