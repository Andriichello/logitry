<?php

namespace App\Http\Resources\User;

use App\Http\Resources\BaseResource;
use App\Http\Resources\Company\CompanyResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

/**
 * Class MeResource.
 *
 * @mixin User
 */
class MeResource extends BaseResource
{
    /**
     * List of columns that are allowed as
     * includes on request.
     *
     * @var array<int|string, string>
     */
    protected array $allowedIncludes = [
        'company' => CompanyResource::class,
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     * @throws Exception
     * @SuppressWarnings(PHPMD)
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'role' => $this->roleInCompany($this->company_id),
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'email_verified_at' => $this->email_verified_at,
            'phone_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            ...$this->getRequested($request),
        ];
    }

    /**
     * @OA\Schema(
     *     schema="MeAppends",
     *     description="Comma-separated list of fields to append to the resource.
              Available appends:",
     *     type="string",
     *     example=""
     * )
     *
     * @OA\Schema(
     *     schema="MeIncludes",
     *     description="Comma-separated list of related resources to include with the resource.
              Available includes: `company`",
     *     type="string",
     *     example="company"
     * )
     */

    /**
     * @OA\Schema(
     *     schema="MeResource",
     *     type="object",
     *     title="Logged-in User (me) Resource",
     *     description="Representation of a logged-in User (me) model.",
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         description="Unique identifier for the user.",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="company_id",
     *         type="integer",
     *         nullable=true,
     *         description="Unique identifier of the company.",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="role",
     *         type="string",
     *         nullable=true,
     *         description="The name of the user.",
     *         example="Admin",
     *         enum={ "Owner", "Admin", "Manager" }
     *     ),
     *     @OA\Property(
     *         property="name",
     *         type="string",
     *         nullable=true,
     *         description="The name of the user.",
     *         example="John Doe"
     *     ),
     *     @OA\Property(
     *         property="email",
     *         type="string",
     *         format="email",
     *         nullable=true,
     *         description="The email address of the user.",
     *         example="johndoe@example.com"
     *     ),
     *     @OA\Property(
     *         property="phone",
     *         type="string",
     *         nullable=true,
     *         description="The phone number of the user.",
     *         example="+1234567890"
     *     ),
     *     @OA\Property(
     *         property="email_verified_at",
     *         type="string",
     *         format="date-time",
     *         nullable=true,
     *         description="Timestamp when the user's email was verified. Null if not verified.",
     *         example="2023-10-12T14:00:00Z"
     *     ),
     *     @OA\Property(
     *         property="phone_verified_at",
     *         type="string",
     *         format="date-time",
     *         nullable=true,
     *         description="Timestamp when the user's phone was verified. Null if not verified.",
     *         example="2023-10-12T14:00:00Z"
     *     ),
     *     @OA\Property(
     *         property="created_at",
     *         type="string",
     *         format="date-time",
     *         nullable=true,
     *         description="Timestamp when the user was created.",
     *         example="2023-01-01T12:00:00Z"
     *     ),
     *     @OA\Property(
     *         property="updated_at",
     *         type="string",
     *         format="date-time",
     *         nullable=true,
     *         description="Timestamp when the user was last updated.",
     *         example="2023-10-10T18:30:00Z"
     *     ),
     *     @OA\Property(
     *         property="company",
     *         type="object",
     *         nullable=true,
     *         description="The company associated with the user.",
     *         ref="#/components/schemas/CompanyResource"
     *     )
     * )
     */
}
