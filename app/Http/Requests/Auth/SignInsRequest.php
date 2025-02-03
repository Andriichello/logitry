<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

/**
 * Class SignInsRequest.
 */
class SignInsRequest extends BaseRequest
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
        ];
    }
}
