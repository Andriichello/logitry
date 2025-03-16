<?php

namespace App\Http\Requests\Web;

use App\Http\Requests\BaseRequest;
use App\Models\Company;

/**
 * Class LandingRequest.
 *
 * @author Andrii Prykhodko <andriichello@gmail.com>
 * @package App\Http\Requests\Web
 */
class LandingRequest extends BaseRequest
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
            //
        ];
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
     * Get the company, which is viewed on the landing page.
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
}
