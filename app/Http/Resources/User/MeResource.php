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
     * @var int|null
     */
    protected ?int $companyId;

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
}
