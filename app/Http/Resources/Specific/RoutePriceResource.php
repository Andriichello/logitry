<?php

namespace App\Http\Resources\Specific;

use App\Http\Resources\BaseResource;
use App\Models\RoutePrice;
use Exception;
use Illuminate\Http\Request;

/**
 * Class RoutePriceResource.
 *
 * @mixin RoutePrice
 */
class RoutePriceResource extends BaseResource
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
            'beg_point_id' => $this->beg_point_id,
            'end_point_id' => $this->end_point_id,
            'name' => $this->name,
            'description' => $this->description,
            'unit' => $this->unit,
            'from' => $this->from,
            'to' => $this->to,
            'currency' => $this->currency,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            ...$this->getRequested($request),
        ];
    }

    /**
     * @OA\Schema(
     *     schema="RoutePrice",
     *     type="object",
     *     title="RoutePrice Resource",
     *     description="Representation of a RoutePrice",
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         description="Unique identifier for the route price",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="route_id",
     *         type="integer",
     *         description="Unique identifier for the associated route",
     *         example=42
     *     ),
     *     @OA\Property(
     *         property="beg_point_id",
     *         type="integer",
     *         nullable=true,
     *         description="Unique identifier for the beginning point of the route",
     *         example=15
     *     ),
     *     @OA\Property(
     *         property="end_point_id",
     *         type="integer",
     *         nullable=true,
     *         description="Unique identifier for the endpoint of the route",
     *         example=30
     *     ),
     *     @OA\Property(
     *         property="name",
     *         type="string",
     *         nullable=true,
     *         description="Name of the route price",
     *         example="Peak Rate"
     *     ),
     *     @OA\Property(
     *         property="description",
     *         type="string",
     *         nullable=true,
     *         description="Description of the route price",
     *         example="Peak hour rates applied"
     *     ),
     *     @OA\Property(
     *         property="unit",
     *         type="string",
     *         description="Sales",
     *         example="Seat",
     *         enum={"Seat", "Weight", "Volume"}
     *     ),
     *     @OA\Property(
     *         property="from",
     *         type="number",
     *         format="float",
     *         description="Starting range price",
     *         example=50.0
     *     ),
     *     @OA\Property(
     *         property="to",
     *         type="number",
     *         format="float",
     *         nullable=true,
     *         description="Ending range price",
     *         example=100.5
     *     ),
     *     @OA\Property(
     *         property="currency",
     *         type="string",
     *         description="Currency for the pricing",
     *         example="USD"
     *     ),
     *     @OA\Property(
     *         property="created_at",
     *         type="string",
     *         format="date-time",
     *         nullable=true,
     *         description="Timestamp when the route price was created",
     *         example="2023-10-01T12:00:00Z"
     *     ),
     *     @OA\Property(
     *         property="updated_at",
     *         type="string",
     *         format="date-time",
     *         nullable=true,
     *         description="Timestamp when the route price was last updated",
     *         example="2023-10-02T14:00:00Z"
     *     ),
     *     @OA\Property(
     *         property="route",
     *         type="object",
     *         nullable=true,
     *         description="The route associated with this pricing",
     *         ref="#/components/schemas/Route"
     *     ),
     *     @OA\Property(
     *         property="beg_point",
     *         type="object",
     *         nullable=true,
     *         description="The beginning point associated with the route",
     *         ref="#/components/schemas/Point"
     *     ),
     *     @OA\Property(
     *         property="end_point",
     *         type="object",
     *         nullable=true,
     *         description="The end point associated with the route",
     *         ref="#/components/schemas/Point"
     *     )
     * )
     */
}
