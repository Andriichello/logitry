<?php

namespace App\Http\Requests\Web;

use App\Http\Requests\BaseRequest;
use App\Models\Company;
use App\Models\User;
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

        return Carbon::parse($beg);
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

        return Carbon::parse($end);
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
}
