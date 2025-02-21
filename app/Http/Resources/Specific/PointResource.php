<?php

namespace App\Http\Resources\Specific;

use App\Http\Resources\BaseResource;
use App\Models\Point;
use Exception;
use Illuminate\Http\Request;

/**
 * Class PointResource.
 *
 * @mixin Point
 */
class PointResource extends BaseResource
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
            'route_id' => $this->route_id,
            'number' => $this->number,
            'name' => $this->name,
            'description' => $this->description,
            'country' => $this->country,
            'city' => $this->city,
            'street' => $this->street,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'travel_time' => $this->travel_time,
            'travel_time_cap' => $this->travel_time_cap,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            ...$this->getRequested($request),
        ];
    }

    /**
     * @OA\Schema(
     *     schema="Point",
     *     type="object",
     *     title="Point Resource",
     *     description="Point resource representation",
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         description="Unique identifier for the point",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="route_id",
     *         type="integer",
     *         description="Identifier of the route associated with the point",
     *         example=42
     *     ),
     *     @OA\Property(
     *         property="number",
     *         type="integer",
     *         description="Point number in its sequence",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="name",
     *         type="string",
     *         nullable=true,
     *         description="Name of the point",
     *         example="Start Point"
     *     ),
     *     @OA\Property(
     *         property="description",
     *         type="string",
     *         nullable=true,
     *         description="Description of the point",
     *         example="This is the starting point of the route."
     *     ),
     *     @OA\Property(
     *         property="country",
     *         type="string",
     *         nullable=true,
     *         description="Country where the point is located",
     *         example="USA"
     *     ),
     *     @OA\Property(
     *         property="city",
     *         type="string",
     *         nullable=true,
     *         description="City where the point is located",
     *         example="New York"
     *     ),
     *     @OA\Property(
     *         property="street",
     *         type="string",
     *         nullable=true,
     *         description="Street where the point is located",
     *         example="5th Avenue"
     *     ),
     *     @OA\Property(
     *         property="latitude",
     *         type="number",
     *         format="float",
     *         description="Latitude coordinate of the point",
     *         example=40.7128
     *     ),
     *     @OA\Property(
     *         property="longitude",
     *         type="number",
     *         format="float",
     *         description="Longitude coordinate of the point",
     *         example=-74.0060
     *     ),
     *     @OA\Property(
     *         property="travel_time",
     *         type="integer",
     *         format="int32",
     *         nullable=true,
     *         description="Travel time to the point in minutes",
     *         example=120
     *     ),
     *     @OA\Property(
     *         property="travel_time_cap",
     *         type="integer",
     *         format="int32",
     *         nullable=true,
     *         description="Maximum allowed travel time to the point in minutes",
     *         example=150
     *     ),
     *     @OA\Property(
     *         property="created_at",
     *         type="string",
     *         format="date-time",
     *         nullable=true,
     *         description="The date and time when the point was created",
     *         example="2023-01-01T10:00:00Z"
     *     ),
     *     @OA\Property(
     *         property="updated_at",
     *         type="string",
     *         format="date-time",
     *         nullable=true,
     *         description="The date and time when the point was last updated",
     *         example="2023-01-01T12:00:00Z"
     *     )
     * )
     */
}
