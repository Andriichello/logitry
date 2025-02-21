<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Interfaces\SignInsHelperInterface;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\SignInsRequest;
use App\Http\Resources\Specific\CompanyResource;
use App\Http\Responses\ApiResponse;

/**
 * Class SignInsController.
 */
class SignInsController extends BaseController
{
    /**
     * @var SignInsHelperInterface
     */
    protected SignInsHelperInterface $helper;

    /**
     * SignInsController constructor.
     *
     * @param SignInsHelperInterface $helper
     */
    public function __construct(SignInsHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Retrieve a list of sign-ins that are available for given email or phone.
     *
     * @param SignInsRequest $request
     *
     * @return ApiResponse
     */
    public function signIns(SignInsRequest $request): ApiResponse
    {
        $email = $request->get('email');
        $phone = $request->get('phone');

        $signIns = empty($phone)
            ? $this->helper->byEmail($email)
            : $this->helper->byPhone($phone);

        foreach ($signIns as &$signIn) {
            $signIn['company'] = new CompanyResource($signIn['company']);
        }

        return ApiResponse::ok([
            'data' => [
                'email' => $email,
                'phone' => $phone,
                'sign_ins' => $signIns,
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/sign-ins",
     *     operationId="signIns",
     *     summary="Retrieve a list of sign-in methods for a user based on email or phone.",
     *     tags={"auth"},
     *
     *     @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         required=false,
     *         description="The phone number of the user. Is required without `email`.
                  Takes precedence over `email` if both are present.",
     *         @OA\Schema(type="string", example="+380991234567")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=false,
     *         description="The email address of the user. Is required without `phone`.",
     *         @OA\Schema(type="string", format="email", example="user@example.com")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="List of available sign-ins.",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="email", type="string", nullable=true, example="user@example.com"),
     *                 @OA\Property(property="phone", type="string", nullable=true, example="+380991234567"),
     *                 @OA\Property(property="sign_ins", type="array",
     *                     @OA\Items(ref ="#/components/schemas/AvailableSignIn")
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    /**
     * @OA\Schema(
     *     schema="AvailableSignIn",
     *     description="Available sign-in object.",
     *     required = {"company", "with_password", "role"},
     *     @OA\Property(property="company", ref ="#/components/schemas/Company"),
     *     @OA\Property(property="with_password", type="boolean", example=true),
     *     @OA\Property(property="role", type="string", nullable=true,
     *        enum={ "Owner", "Admin", "Manager" }),
     * )
     */
}
