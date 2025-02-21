<?php

namespace App\Http\Resources\Specific;

use App\Http\Resources\BaseResource;
use Exception;
use Illuminate\Http\Request;

/**
 * Class BoundsResource.
 */
class BoundsResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array{
     *       northEast: array{
     *           latitude: float,
     *           longitude: float,
     *       },
     *       southWest: array{
     *            latitude: float,
     *            longitude: float,
     *       },
     *   }
     * @throws Exception
     * @SuppressWarnings(PHPMD)
     */
    public function toArray(Request $request): array
    {
        return [
            'northEast' => data_get($this, 'northEast', []),
            'southWest' => data_get($this, 'southWest', []),
        ];
    }

    /**
     * @OA\Schema(
     *     schema="Bounds",
     *     type="object",
     *     title="Coordinates Bounds Resource",
     *     description="Represents a set of geographical coordinates defining bounds",
     *     @OA\Property(
     *         property="northEast",
     *         type="object",
     *         description="The northeastern bound coordinates",
     *         @OA\Property(
     *             property="latitude",
     *             type="number",
     *             format="float",
     *             description="Latitude of the northeastern point",
     *             example=40.7128
     *         ),
     *         @OA\Property(
     *             property="longitude",
     *             type="number",
     *             format="float",
     *             description="Longitude of the northeastern point",
     *             example=-74.0060
     *         )
     *     ),
     *     @OA\Property(
     *         property="southWest",
     *         type="object",
     *         description="The southwestern bound coordinates",
     *         @OA\Property(
     *             property="latitude",
     *             type="number",
     *             format="float",
     *             description="Latitude of the southwestern point",
     *             example=34.0522
     *         ),
     *         @OA\Property(
     *             property="longitude",
     *             type="number",
     *             format="float",
     *             description="Longitude of the southwestern point",
     *             example=-118.2437
     *         )
     *     )
     * )
     */
}
