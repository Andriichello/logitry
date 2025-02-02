<?php

namespace App\Http\Resources\Company;

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
}
