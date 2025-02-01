<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;
use App\Models\Company;
use Illuminate\Validation\Rule;

/**
 * Class LoginRequest.
 *
 * @author Andrii Prykhodko <andriichello@gmail.com>
 * @package App\Http\Requests\Auth
 */
class LoginRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required_without:phone',
                'email',
            ],
            'phone' => [
                'required_without:email',
                'regex:/^\+?[0-9]{10,15}$/',
            ],
            'password' => [
                'required',
                'string',
            ],
            'abbreviation' => [
                'required',
                'string',
                Rule::exists(Company::class, 'abbreviation')
            ],
        ];
    }

    /**
     * Get form request fields' override values.
     *
     * @return array
     */
    protected function overrides(): array
    {
        return [
            'abbreviation' => strtolower($this->get('abbreviation')),
        ];
    }

    /**
     * Get the company id, which should be used within the request.
     *
     * @return int|null
     */
    public function companyId(): ?int
    {
        $abb = $this->get('abbreviation');

        return Company::findBy(abb: $abb)?->id;
    }

    /**
     * Get credentials, namely: `phone`, `email`, `password`, `project_id`.
     *
     * @return array
     */
    public function credentials(): array
    {
        return [
            'company_id' => $this->companyId(),
            'email' => $this->get('email'),
            'phone' => $this->get('phone'),
            'password' => $this->get('password'),
        ];
    }
}
