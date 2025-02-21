<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Specific\MeResource;
use App\Http\Responses\ApiResponse;
use App\Models\AccessToken;
use Illuminate\Auth\AuthenticationException;

/**
 * Class LoginController.
 */
class LoginController extends BaseController
{
    /**
     * Login user by credentials (email/phone & password).
     *
     * @param LoginRequest $request
     *
     * @return ApiResponse
     * @throws AuthenticationException
     */
    public function login(LoginRequest $request): ApiResponse
    {
        $user = $request->authenticate();
        $token = $user->createToken($request->userAgent() ?? 'Unknown');

        $companyId = $request->companyId();

        /** @var AccessToken $accessToken */
        $accessToken = $token->accessToken;
        $accessToken->company_id = $companyId;
        $accessToken->role = $user->roleInCompany($companyId);
        $accessToken->save();

        $user->company_id = $companyId;

        return ApiResponse::ok([
            'data' => [
                'token_type' => 'Bearer',
                'token' => $token->plainTextToken,
                'expires_at' => $accessToken->expires_at,
                'expires_in' => $accessToken->expires_at
                    ? (int) now()->diffInSeconds($accessToken->expires_at) : null,
                'user' => new MeResource($user),
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     operationId="login",
     *     summary="Log in using email/phone and password",
     *     tags={"auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref = "#/components/schemas/LoginRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Logged in successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token_type", type="string", example="Bearer"),
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJ..."),
     *                 @OA\Property(property="expires_at", type="string", format="date-time",
     *                     example="2023-12-31T23:59:59.000Z"),
     *                 @OA\Property(property="expires_in", type="integer", nullable=true, example=3600),
     *                 @OA\Property(property="user", ref = "#/components/schemas/Me")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to authenticate.")
     *         )
     *     )
     * )
     */
}
