<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\User\MeResource;
use App\Http\Responses\ApiResponse;
use App\Models\AccessToken;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

/**
 * Class LoginController.
 */
class LoginController extends BaseController
{
    /**
     * Login user by credentials (email & password).
     *
     * @param LoginRequest $request
     *
     * @return ApiResponse
     * @throws AuthenticationException
     */
    public function login(LoginRequest $request): ApiResponse
    {
        $user = $this->findUserByCredentials($credentials = $request->credentials());
        $token = $user->createToken($request->userAgent());

        /** @var AccessToken $accessToken */
        $accessToken = $token->accessToken;
        $accessToken->company_id = $credentials['company_id'];
        $accessToken->role = $user->roleInCompany($credentials['company_id']);
        $accessToken->save();

        return ApiResponse::ok([
            'data' => [
                'token_type' => 'Bearer',
                'token' => $token->plainTextToken,
                'expires_at' => $accessToken->expires_at,
                'expires_in' => $accessToken->expires_at
                    ? (int) now()->diffInSeconds($accessToken->expires_at) : null,
                'user' => new MeResource($user, $credentials['company_id']),
            ],
        ]);
    }

    /**
     * Attempt to log in user by credentials.
     *
     * @param array $credentials
     *
     * @return User|null
     */
    public function attemptAuth(array $credentials): ?User
    {
        /** @var User|null $user */
        /** @phpstan-ignore-next-line */
        $user = Auth::guard('web')->attempt($credentials)
            ? Auth::guard('web')->user() : null;

        return $user;
    }

    /**
     * Find user by credentials.
     *
     * @param array $credentials
     *
     * @return User|null
     * @throws AuthenticationException
     */
    protected function findUserByCredentials(array $credentials): ?User
    {
        $means = empty($credentials['phone'])
            ? 'email' : 'phone';

        $params = [
            $means => $credentials[$means],
            'password' => $credentials['password'],
        ];

        $user = $this->attemptAuth($params);

        if (!$user || !$user->isPartOf($credentials['company_id'])) {
            throw new AuthenticationException('Failed to authenticate.');
        }

        return $user;
    }
}
