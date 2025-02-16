<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Web\MapRequest;
use App\Http\Resources\Company\CompanyResource;
use Inertia\Response;
use Inertia\ResponseFactory;

/**
 * Class MapController.
 */
class MapController extends BaseController
{
    /**
     * Returns map view.
     *
     * @param MapRequest $request
     *
     * @return Response|ResponseFactory
     * @SuppressWarnings(PHPMD)
     */
    public function view(MapRequest $request): Response|ResponseFactory
    {
        $trips = [
            [
                'id' => 1,
                'type' => 'Transfer',
                'status' => 'Available', // Sold-Out, Cancelled, Unavailable
                'name' => 'Trip One',
                'description' => 'Trip One Description',
                'date' => now()->toDateString(),
                'points' => [
                    [
                        'city' => 'Uzhhorod',
                        'country' => 'UA',
                        'street' => 'Klympusha 3',
                        'time' => now()->setTime(12, 30),
                        'longitude' => 22.30242477063706,
                        'latitude' => 48.62379120458644,
                    ],
                    [
                        'city' => 'Košice',
                        'country' => 'SK',
                        'street' => 'Staničné námestie',
                        'time' => now()->setTime(15, 30),
                        'longitude' => 21.250305471055327,
                        'latitude' => 48.719185437015966,
                    ],
                ],
                'vehicle' => [
                    'id' => 1,
                    'manufacturer' => 'Renault',
                    'model' => 'Megan',
                    'year' => '2015',
                    'color' => 'Olive',
                ],
                'driver' => [
                    'id' => 1,
                    'name' => 'Andrii',
                    'phone' => '+380991234567',
                ],
                'seats' => 4,
                'seats_booked' => 2,
                'price' => 30,
                'currency' => 'EUR',
                'contact' => [
                    'phone' => '+380991234567',
                ]
            ],
            [
                'id' => 2,
                'type' => 'Transfer',
                'status' => 'Available', // Sold-Out, Cancelled, Unavailable
                'name' => 'Trip Two',
                'description' => 'Trip Two Description',
                'date' => now()->toDateString(),
                'points' => [
                    [
                        'city' => 'Uzhhorod',
                        'country' => 'UA',
                        'street' => 'Klympusha 3',
                        'time' => now()->setTime(17, 10),
                        'longitude' => 22.715706610539883,
                        'latitude' => 48.44364455106009
                    ],
                    [
                        'city' => 'Nyíregyháza',
                        'country' => 'HU',
                        'street' => null,
                        'time' => now()->setTime(20, 20),
                        'longitude' => 21.726890923480113,
                        'latitude' => 47.960575546680616
                    ],
                    [
                        'city' => 'Debrecen',
                        'country' => 'HU',
                        'street' => null,
                        'time' => now()->setTime(22, 00),
                        'longitude' => 21.629276440910246,
                        'latitude' => 47.52632324964148
                    ],
                    [
                        'city' => 'Budapest',
                        'country' => 'HU',
                        'street' => 'Kerepesi út 2-4',
                        'time' => now()->setTime(23, 50),
                        'longitude' => 19.037301044504176,
                        'latitude' => 47.502402679660264
                    ]
                ],
                'vehicle' => [
                    'id' => 2,
                    'manufacturer' => 'Renault',
                    'model' => 'Traffic',
                    'year' => '2020',
                    'color' => 'Dark Red',
                ],
                'driver' => [
                    'id' => 1,
                    'name' => 'Dennis',
                    'phone' => '+380991234987',
                ],
                'seats' => 9,
                'seats_booked' => 1,
                'price' => 50,
                'currency' => 'EUR',
                'contact' => [
                    'phone' => '+380991234987',
                ]
            ],
        ];

        $props = [
            'company' => ($company = $request->company())
                ? new CompanyResource($company) : null,
            'trips' => $trips,
        ];

        return inertia('Map', $props);
    }


    /**
     * @OA\Schema(
     *     schema="Trip",
     *     type="object",
     *     description="Trip details",
     *     required={"id", "type", "status",
     *     "name", "description", "date",
     *     "points", "vehicle", "driver",
     *     "seats", "seats_booked",
     *     "price", "currency",
     *     "contact"},
     *     @OA\Property(property="id", type="integer", description="Unique identifier for the trip."),
     *     @OA\Property(property="type", type="string", description="Type of the trip, e.g., Transfer."),
     *     @OA\Property(property="status", type="string",
     *     enum={"Available", "Sold-Out", "Cancelled", "Unavailable"},
     *     description="Current status of the trip."),
     *     @OA\Property(property="name", type="string", description="Name of the trip."),
     *     @OA\Property(property="description", type="string", description="Description of the trip."),
     *     @OA\Property(property="date", type="string", format="date", description="Date of the trip."),
     *     @OA\Property(
     *         property="points",
     *         type="array",
     *         description="List of points in the trip route.",
     *         @OA\Items(ref="#/components/schemas/TripPoint")
     *     ),
     *     @OA\Property(property="vehicle", ref="#/components/schemas/TripVehicle"),
     *     @OA\Property(property="driver", ref="#/components/schemas/TripDriver"),
     *     @OA\Property(property="seats", type="integer", description="Total number of seats in the vehicle."),
     *     @OA\Property(property="seats_booked", type="integer", description="Number of already booked seats."),
     *     @OA\Property(property="price", type="number", format="float", description="Price per seat."),
     *     @OA\Property(property="currency", type="string", description="Currency for the price."),
     *     @OA\Property(property="contact", ref="#/components/schemas/TripContact")
     * )
     *
     * @OA\Schema(
     *     schema="TripPoint",
     *     type="object",
     *     description="Point of the trip route",
     *     required={"city", "country", "street", "time", "longitude", "latitude"},
     *     @OA\Property(property="city", type="string", description="City of the point."),
     *     @OA\Property(property="country", type="string", description="Country of the point."),
     *     @OA\Property(property="street", type="string", nullable=true, description="Street address of the point."),
     *     @OA\Property(property="time", type="string", format="time", description="Scheduled time at this point."),
     *     @OA\Property(property="longitude", type="number", format="float", description="Longitude coordinate."),
     *     @OA\Property(property="latitude", type="number", format="float", description="Latitude coordinate.")
     * )
     *
     * @OA\Schema(
     *     schema="TripVehicle",
     *     type="object",
     *     description="Details of the trip's vehicle",
     *     required={"id", "manufacturer", "model", "year", "color"},
     *     @OA\Property(property="id", type="integer", description="Vehicle identifier."),
     *     @OA\Property(property="manufacturer", type="string", description="Manufacturer of the vehicle."),
     *     @OA\Property(property="model", type="string", description="Model of the vehicle."),
     *     @OA\Property(property="year", type="string", description="Year of manufacture."),
     *     @OA\Property(property="color", type="string", description="Color of the vehicle.")
     * )
     *
     * @OA\Schema(
     *     schema="TripDriver",
     *     type="object",
     *     description="Information about the driver",
     *     required={"id", "name", "phone"},
     *     @OA\Property(property="id", type="integer", description="Driver identifier."),
     *     @OA\Property(property="name", type="string", description="Name of the driver."),
     *     @OA\Property(property="phone", type="string", description="Driver contact number.")
     * )
     *
     * @OA\Schema(
     *     schema="TripContact",
     *     type="object",
     *     description="Contact details for the trip",
     *     required={"phone"},
     *     @OA\Property(property="phone", type="string", description="Contact phone number.")
     * )
     */
}
