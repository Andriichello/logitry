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
}
