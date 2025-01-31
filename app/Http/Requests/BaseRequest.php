<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Throwable;

/**
 * Class BaseRequest.
 */
abstract class BaseRequest extends FormRequest
{
    /**
     * Message, which should be displayed on failed authorization attempt.
     *
     * @var string
     */
    protected string $message = 'You are not authorized to perform this request.';

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws AuthorizationException
     */
    protected function failedAuthorization(): void
    {
        throw new AuthorizationException($this->message);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    /**
     * Get form request fields' default values.
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [];
    }

    /**
     * Get form request fields' override values.
     *
     * @return array
     */
    protected function overrides(): array
    {
        return [];
    }

    /**
     * Get the company id, which should be used within the request.
     *
     * @return int|null
     */
    public function companyId(): ?int
    {
        /** @var User|null $user */
        $user = $this->user();

        try {
            $companyId = $user?->companyId();
        } catch (Throwable) {
            $companyId = null;
        }

        return $companyId ?: $this->get('company_id');
    }

    /**
     * Get the validator instance for the request.
     *
     * @return Validator
     */
    protected function getValidatorInstance(): Validator
    {
        $defaults = $this->defaults();

        if (!empty($defaults)) {
            $this->mergeIfMissing($defaults);
        }

        $overrides = $this->overrides();

        if (!empty($overrides)) {
            $this->merge($overrides);
        }

        return parent::getValidatorInstance();
    }

    /**
     * Clear all parameters of the request.
     *
     * @return static
     */
    public function clear(): static
    {
        foreach ($this->request->keys() as $key) {
            $this->request->remove($key);
        }

        return $this;
    }

    /**
     * Create a new request instance from the given Laravel request.
     *
     * @param Request $from
     * @param Request|null $to
     * @param array|null $data
     *
     * @return static
     */
    public static function createFrom(Request $from, $to = null, ?array $data = null): static
    {
        $obj = parent::createFrom($from, $to);

        if ($data !== null) {
            $obj->clear();
            $obj->merge($data);
        }

        $obj->setContainer(app());
        $obj->getValidatorInstance();
        $obj->setRedirector(app(Redirector::class));

        return $obj;
    }

    /**
     * Get the validated data from the request.
     *
     * @param array|int|string|null $key
     * @param mixed $default
     *
     * @return mixed
     * @throws ValidationException
     */
    public function validated($key = null, $default = null): mixed
    {
        return data_get($this->validator->validate(), $key, $default);
    }
}
