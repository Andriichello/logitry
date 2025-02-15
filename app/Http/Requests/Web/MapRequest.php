<?php

namespace App\Http\Requests\Web;

use App\Http\Requests\BaseRequest;
use App\Models\Company;
use App\Models\User;

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
        ];
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
