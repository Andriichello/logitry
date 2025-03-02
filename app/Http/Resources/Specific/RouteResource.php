<?php

namespace App\Http\Resources\Specific;

use App\Helpers\BoundsHelper;
use App\Http\Resources\BaseResource;
use App\Models\Route;
use App\Models\RoutePrice;
use Exception;
use Illuminate\Http\Request;

/**
 * Class RouteResource.
 *
 * @mixin Route
 */
class RouteResource extends BaseResource
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
        /** @var RoutePrice|null $basePrice */
        $basePrice = $this->prices
            ->whereNull('beg_point_id')
            ->whereNull('end_point_id')
            ->sortBy('id')
            ->first();

        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'contact_id' => $this->contact_id,
            'vehicle_id' => $this->vehicle_id,
            'driver_id' => $this->driver_id,
            'name' => $this->name,
            'description' => $this->description,
            'travel_time' => $this->travel_time,
            'travel_time_cap' => $this->travel_time_cap,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'bounds' => (new BoundsHelper())
                ->forPoints($this->points),
            'points' => $this->points,
            'prices' => RoutePriceResource::collection($this->prices),
            'base_price' => RoutePriceResource::make($basePrice),
            ...$this->getRequested($request),
        ];
    }

    /**
     * @OA\Schema(
     *     schema="Route",
     *     type="object",
     *     title="Route Resource",
     *     description="Representation of a Route",
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         description="Unique identifier for the route",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="company_id",
     *         type="integer",
     *         description="Identifier for the company associated with the route",
     *         example=10
     *     ),
     *     @OA\Property(
     *         property="contact_id",
     *         type="integer",
     *         nullable=true,
     *         description="Identifier for the contact person related to the route",
     *         example=45
     *     ),
     *     @OA\Property(
     *         property="vehicle_id",
     *         type="integer",
     *         nullable=true,
     *         description="Identifier for the vehicle used in the route",
     *         example=23
     *     ),
     *     @OA\Property(
     *         property="driver_id",
     *         type="integer",
     *         nullable=true,
     *         description="Identifier for the driver assigned to the route",
     *         example=52
     *     ),
     *     @OA\Property(
     *         property="name",
     *         type="string",
     *         nullable=true,
     *         description="Name of the route",
     *         example="Downtown Delivery"
     *     ),
     *     @OA\Property(
     *         property="description",
     *         type="string",
     *         nullable=true,
     *         description="Details about the route",
     *         example="Morning delivery route covering downtown area."
     *     ),
     *     @OA\Property(
     *         property="travel_time",
     *         type="integer",
     *         nullable=true,
     *         description="Total travel time in minutes",
     *         example=635
     *     ),
     *     @OA\Property(
     *         property="travel_time_cap",
     *         type="integer",
     *         nullable=true,
     *         description="Cap (max) for total travel time",
     *         example=835
     *     ),
     *     @OA\Property(
     *         property="created_at",
     *         type="string",
     *         format="date-time",
     *         nullable=true,
     *         description="Timestamp when the route was created",
     *         example="2023-10-01T12:00:00Z"
     *     ),
     *     @OA\Property(
     *         property="updated_at",
     *         type="string",
     *         format="date-time",
     *         nullable=true,
     *         description="Timestamp when the route was last updated",
     *         example="2023-10-02T14:00:00Z"
     *     ),
     *     @OA\Property(
     *         property="company",
     *         type="object",
     *         nullable=true,
     *         description="The company associated with the route",
     *         ref="#/components/schemas/Company"
     *     ),
     *     @OA\Property(
     *         property="contact",
     *         type="object",
     *         nullable=true,
     *         description="Contact information associated with the route",
     *     ),
     *     @OA\Property(
     *         property="vehicle",
     *         type="object",
     *         nullable=true,
     *         description="Vehicle associated with the route",
     *     ),
     *     @OA\Property(
     *         property="driver",
     *         type="object",
     *         nullable=true,
     *         description="Driver of the route",
     *     ),
     *     @OA\Property(
     *         property="points",
     *         type="array",
     *         description="Points included in the route",
     *         @OA\Items(ref="#/components/schemas/Point"),
     *     ),
     *     @OA\Property(
     *         property="bounds",
     *         type="object",
     *         nullable=true,
     *         description="Geographical bounds for the route",
     *         ref="#/components/schemas/Bounds"
     *     ),
     *     @OA\Property(
     *         property="prices",
     *         type="array",
     *         nullable=true,
     *         description="Prices of the in the route",
     *         @OA\Items(ref="#/components/schemas/RoutePrice"),
     *     ),
     *     @OA\Property(
     *         property="base_price",
     *         type="object",
     *         nullable=true,
     *         description="Base price of the route",
     *         ref="#/components/schemas/RoutePrice",
     *     ),
     * )
     */
}
