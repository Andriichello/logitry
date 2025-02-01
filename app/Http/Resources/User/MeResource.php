<?php

namespace App\Http\Resources\User;

use App\Http\Resources\BaseResource;
use App\Models\Company;
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
     * MeResource constructor.
     *
     * @param $resource
     * @param Company|int|null $companyId
     */
    public function __construct($resource, Company|int|null $companyId = null)
    {
        parent::__construct($resource);

        if ($companyId instanceof Company) {
            $companyId = $companyId->id;
        }

        $this->companyId = $companyId;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     * @throws Exception
     * @SuppressWarnings(PHPMD)
     */
    public function toArray(Request $request): array
    {
        if (empty($this->companyId)) {
            $user = $request->user();

            if ($user instanceof User && $user->id === $this->id) {
                $this->companyId = $user->companyId();
            }
        }

        return [
            'id' => $this->id,
            'company_id' => $this->companyId,
            'role' => $this->roleInCompany($this->companyId),
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'phone_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            ...$this->getRequested($request),
        ];
    }
}
