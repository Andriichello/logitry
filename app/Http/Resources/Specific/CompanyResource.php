<?php

namespace App\Http\Resources\Specific;

use App\Http\Resources\BaseResource;
use App\Models\Company;
use Exception;
use Illuminate\Http\Request;

/**
 * Class CompanyResource.
 *
 * @mixin Company
 */
class CompanyResource extends BaseResource
{
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
            'abbreviation' => $this->abbreviation,
            'name' => $this->name,
            'realm' => $this->realm,
            'plan' => $this->plan,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            ...$this->getRequested($request),
        ];
    }

    /**
     * @OA\Schema(
     *     schema="CompanyAppends",
     *     description="Comma-separated list of fields to append to the resource.
              Available appends:",
     *     type="string",
     *     example=""
     * )
     *
     * @OA\Schema(
     *     schema="CompanyIncludes",
     *     description="Comma-separated list of related resources to include with the resource.
              Available includes:",
     *     type="string",
     *     example=""
     * )
     */

    /**
     * @OA\Schema(
     *     schema="Company",
     *     type="object",
     *     description="Company resource representation",
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         description="The unique identifier of the company",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="abbreviation",
     *         type="string",
     *         description="The abbreviation of the company",
     *         example="ABC"
     *     ),
     *     @OA\Property(
     *         property="name",
     *         type="string",
     *         description="The name of the company",
     *         example="ABC Corporation"
     *     ),
     *     @OA\Property(
     *         property="realm",
     *         type="string",
     *         nullable=true,
     *         description="The realm associated with the company",
     *         example="Logistics",
     *         enum={ "Logistics", "Transfers" }
     *     ),
     *     @OA\Property(
     *         property="plan",
     *         type="string",
     *         nullable=true,
     *         description="The subscription plan of the company",
     *         example="Free"
     *     ),
     *     @OA\Property(
     *         property="created_at",
     *         type="string",
     *         format="date-time",
     *         nullable=true,
     *         description="The date and time the company was created",
     *         example="2023-01-01T12:00:00Z"
     *     ),
     *     @OA\Property(
     *         property="updated_at",
     *         type="string",
     *         format="date-time",
     *         nullable=true,
     *         description="The date and time the company was last updated",
     *         example="2023-01-10T15:00:00Z"
     *     ),
     * )
     */
}
