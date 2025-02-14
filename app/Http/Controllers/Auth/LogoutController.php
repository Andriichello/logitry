<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\LogoutRequest;
use App\Http\Responses\ApiResponse;
use App\Models\AccessToken;
use App\Models\User;
use Throwable;

/**
 * Class LogoutController.
 */
class LogoutController extends BaseController
{
    /**
     * Logout user using an access token.
     *
     * @param LogoutRequest $request
     *
     * @return ApiResponse
     */
    public function logout(LogoutRequest $request): ApiResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        if ($user instanceof User) {
            $token = $user->currentAccessToken();

            if ($token instanceof AccessToken) {
                $token->expires_at = now();
                $token->save();

                return ApiResponse::ok(msg: 'Successfully logged out.');
            }
        }

        return ApiResponse::notFound(msg: 'Failed to log out.');
    }

    /**
     * Logout user using a cookie-based session.
     *
     * @param LogoutRequest $request
     *
     * @return ApiResponse
     */
    public function webLogout(LogoutRequest $request): ApiResponse
    {
        try {
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return ApiResponse::ok(msg: 'Successfully logged out.');
        } catch (Throwable) {
            return ApiResponse::notFound(msg: 'Failed to log out.');
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/logout",
     *     operationId="logout",
     *     summary="Log out using an access token",
     *     security={{"bearerAuth": {}}},
     *     tags={"auth"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully logged out.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Failed to log out",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to log out.")
     *         )
     *     )
     * )
     *
     * @OA\Delete(
     *     path="/web/logout",
     *     operationId="webLogout",
     *     summary="Log out using an access token",
     *     security={{"cookieAuth": {}}},
     *     tags={"auth.web"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully logged out.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Failed to log out",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to log out.")
     *         )
     *     )
     * )
     */
}
