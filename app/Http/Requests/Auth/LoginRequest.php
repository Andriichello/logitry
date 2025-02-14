<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\SessionGuard;
use Illuminate\Support\Facades\Auth;
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
                'required',
                'string',
                Rule::exists(Company::class, 'abbreviation')
            ],
            'email' => [
                'required_without:phone',
                'sometimes',
                'nullable',
                'string',
                'email',
            ],
            'phone' => [
                'required_without:email',
                'sometimes',
                'nullable',
                'string',
                'regex:/^\+?[0-9]{10,15}$/',
            ],
            'password' => [
                'required',
                'string',
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
        if (empty($this->company)) {
            $abb = $this->get('abbreviation');
            $this->company = Company::findBy(abb: $abb);
        }

        return $this->company?->id;
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

    /**
     * Attempts to log user in by given credentials.
     *
     * @param array $credentials
     * @param bool $remember
     *
     * @return User|null
     * @SuppressWarnings(PHPMD)
     */
    public function attempt(array $credentials, bool $remember = false): ?User
    {
        /** @var SessionGuard $guard */
        $guard = Auth::guard('web');

        if ($guard->attempt($credentials, $remember)) {
            /** @var User $user */
            $user = $guard->user();
        }

        return $user ?? null;
    }

    /**
     * Attempts to log user in and throws an exception if can't.
     *
     * @return User
     * @throws AuthenticationException
     */
    public function authenticate(): User
    {
        $credentials = $this->credentials();

        $means = empty($credentials['phone'])
            ? 'email' : 'phone';

        $params = [
            $means => $credentials[$means],
            'password' => $credentials['password'],
        ];

        $user = $this->attempt($params);

        if (!$user) {
            throw new AuthenticationException('Failed to authenticate.');
        }

        if (!$user->isPartOf($credentials['company_id'])) {
            session()->invalidate();
            throw new AuthenticationException('You are not part of this company.');
        }

        return $user;
    }

    /**
     * @OA\Schema(
     *   schema="LoginRequest",
     *   type="object",
     *   description="Request schema for logging in using credentials.",
     *   required={"abbreviation", "password"},
     *   @OA\Property(
     *     property="abbreviation",
     *     type="string",
     *     example="company",
     *     description="Abbreviation of the company. Is required."
     *   ),
     *   @OA\Property(
     *     property="email",
     *     type="string",
     *     format="email",
     *     example="user@example.com",
     *     description="Email of the user. Required if 'phone' is not provided."
     *   ),
     *   @OA\Property(
     *     property="phone",
     *     type="string",
     *     example="+380991234567",
     *     description="Phone number of the user. Required if 'email' is not provided."
     *   ),
     *   @OA\Property(
     *     property="password",
     *     type="string",
     *     format="password",
     *     description="User's password. Is required."
     *   )
     * )
     */
}
