<?php

namespace App\Http\Resources\Specific;

use App\Http\Resources\BaseResource;
use Exception;
use Illuminate\Http\Request;

/**
 * Class TripHighlightResource.
 */
class TripHighlightResource extends BaseResource
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
        $ids = explode(',', data_get($this->resource, 'ids', ''));
        $ids = array_map(fn($id) => (int) $id, $ids);

        return [
            'ids' => $ids,
            'date' => (string) data_get($this->resource, 'date'),
            'count' => (int) data_get($this->resource, 'count'),
        ];
    }

    /**
     * @OA\Schema(
     *     schema="TripHighlight",
     *     type="object",
     *     title="Trips Highlight Resource",
     *     description="Representation of a Trips Highlight",
     *
     *     @OA\Property(
     *         property="ids",
     *         type="array",
     *         description="Ids of the trips that are included in the count",
     *         @OA\Items(type="integer"),
     *     ),
     *     @OA\Property(
     *         property="date",
     *         type="string",
     *         format="date",
     *         description="Departure date of the trips",
     *         example="2023-10-15"
     *     ),
     *     @OA\Property(
     *         property="count",
     *         type="integer",
     *         description="Number of trips on the given date",
     *         example=3
     *     ),
     * )
     */
}
