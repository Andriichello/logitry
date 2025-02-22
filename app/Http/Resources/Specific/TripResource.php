<?php

namespace App\Http\Resources\Specific;

use App\Http\Resources\BaseResource;
use App\Models\Trip;
use Exception;
use Illuminate\Http\Request;

/**
 * Class TripResource.
 *
 * @mixin Trip
 */
class TripResource extends BaseResource
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
            'reversed' => $this->reversed,
            'vehicle_id' => $this->vehicle_id,
            'driver_id' => $this->driver_id,
            'contact_id' => $this->contact_id,
            'status' => $this->status,
            'metadata' => $this->metadata,
            'has_happened' => $this->has_happened,
            'is_happening' => $this->is_happening,
            'will_happen' => $this->will_happen,
            'departs_at' => $this->departs_at,
            'arrives_at' => $this->arrives_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            ...$this->getRequested($request),
        ];
    }

    /**
     * @OA\Schema(
     *     schema="Trip",
     *     type="object",
     *     title="Trip Resource",
     *     description="Representation of a Trip",
     *
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         description="Unique identifier for the trip",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="route_id",
     *         type="integer",
     *         description="Identifier for the associated route",
     *         example=10
     *     ),
     *     @OA\Property(
     *         property="reversed",
     *         type="boolean",
     *         description="Indicates if the route direction is reversed",
     *         example=true
     *     ),
     *     @OA\Property(
     *         property="vehicle_id",
     *         type="integer",
     *         nullable=true,
     *         description="Identifier for the vehicle assigned to the trip",
     *         example=15
     *     ),
     *     @OA\Property(
     *         property="driver_id",
     *         type="integer",
     *         nullable=true,
     *         description="Identifier for the driver assigned to the trip",
     *         example=8
     *     ),
     *     @OA\Property(
     *         property="contact_id",
     *         type="integer",
     *         nullable=true,
     *         description="Identifier for the contact person related to the trip",
     *         example=45
     *     ),
     *     @OA\Property(
     *         property="status",
     *         type="string",
     *         description="Current status of the trip",
     *         enum={"Draft", "Available", "Unavailable", "In Progress", "Finished", "Cancelled"}
     *     ),
     *     @OA\Property(
     *         property="metadata",
     *         type="object",
     *         nullable=true,
     *         description="Additional metadata for the trip",
     *     ),
     *     @OA\Property(
     *         property="departs_at",
     *         type="string",
     *         format="date-time",
     *         description="Planned departure time of the trip",
     *         example="2023-10-15T08:00:00Z"
     *     ),
     *     @OA\Property(
     *         property="arrives_at",
     *         type="string",
     *         format="date-time",
     *         nullable=true,
     *         description="Expected arrival time of the trip",
     *         example="2023-10-15T10:00:00Z"
     *     ),
     *     @OA\Property(
     *         property="created_at",
     *         type="string",
     *         format="date-time",
     *         nullable=true,
     *         description="Timestamp when the trip was created",
     *         example="2023-10-01T12:00:00Z"
     *     ),
     *     @OA\Property(
     *         property="updated_at",
     *         type="string",
     *         format="date-time",
     *         nullable=true,
     *         description="Timestamp when the trip was last updated",
     *         example="2023-10-02T14:00:00Z"
     *     ),
     * )
     */
}
