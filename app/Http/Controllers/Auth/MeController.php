<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\MeRequest;
use App\Http\Resources\User\MeResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;

/**
 * Class MeController.
 */
class MeController extends BaseController
{
    /**
     * Get currently logged-in user.
     *
     * @param MeRequest $request
     *
     * @return ApiResponse
     */
    public function me(MeRequest $request): ApiResponse
    {
        $user = $request->user();

        if ($user instanceof User) {
            return ApiResponse::ok([
                'data' => new MeResource($user)
            ]);
        }

        return ApiResponse::error();
    }
}
