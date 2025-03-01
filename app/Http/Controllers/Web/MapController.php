<?php

namespace App\Http\Controllers\Web;

use App\Helpers\BoundsHelper;
use App\Helpers\CountriesHelper;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Web\MapRequest;
use App\Http\Resources\Specific\CompanyResource;
use App\Http\Resources\Specific\RouteResource;
use App\Http\Resources\Specific\TripHighlightResource;
use App\Http\Resources\Specific\TripResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;
use Inertia\ResponseFactory;

/**
 * Class MapController.
 */
class MapController extends BaseController
{
    /**
     * Returns map data.
     *
     * @param MapRequest $request
     *
     * @return ApiResponse|RedirectResponse
     * @SuppressWarnings(PHPMD)
     */
    public function get(MapRequest $request): ApiResponse|RedirectResponse
    {
        $company = $request->company();

        if ($company === null) {
            $props = [
                'company' => null,
                'routes' => [],
                'bounds' => null,
                'trips' => null,
                'filters' => $request->filters(),
            ];

            return ApiResponse::ok($props);
        }

        if ($request->missing('beg') || $request->beg()?->diffInDays(now()) > 2) {
            return redirect()
                ->route(
                    'web.map.data',
                    [
                        ...$request->query(),
                        'beg' => now()->format('Y-m-d'),
                    ]
                );
        }

        $routes = $request->routes()->get();
        $trips = $request->trips()->get();

        $tripHighlights = $request->tripsForHighlights()
            ->highlights()
            ->get();

        $props = [
            'company' => ($company = $request->company())
                ? new CompanyResource($company) : null,
            'routes' => RouteResource::collection($routes),
            'bounds' => (new BoundsHelper())
                ->forRoutes($routes->all(), 0.05),
            'trips' => TripResource::collection($trips),
            'trip_highlights' => TripHighlightResource::collection($tripHighlights),
            'filters' => $request->filters(),
        ];

        return ApiResponse::ok($props);
    }


    /**
     * Returns map view.
     *
     * @param MapRequest $request
     *
     * @return RedirectResponse|Response|ResponseFactory
     * @SuppressWarnings(PHPMD)
     */
    public function view(MapRequest $request): RedirectResponse|Response|ResponseFactory
    {
        $company = $request->company();

        if ($company === null) {
            $props = [
                'company' => null,
                'routes' => [],
                'bounds' => null,
                'filters' => $request->filters(),
                'trips' => null,
                'trip_highlights' => null,
            ];

            return inertia('Map', $props);
        }

        if ($request->missing('beg') || $request->beg()?->diffInDays(now()) > 2) {
            return redirect()
                ->route(
                    'web.map.view',
                    [
                        ...$request->query(),
                        'beg' => now()->format('Y-m-d'),
                    ]
                );
        }

        $routes = $request->routes()->get();

        $props = [
            'company' => ($company = $request->company())
                ? new CompanyResource($company) : null,
            'routes' => RouteResource::collection($routes),
            'bounds' => (new BoundsHelper())
                ->forRoutes($routes->all(), 0.05),
            'filters' => $request->filters(),
            'trips' => Inertia::defer(
                fn() => TripResource::collection($request->trips()->get()),
                'trips'
            ),
            'trip_highlights' => Inertia::defer(
                fn() => TripHighlightResource::collection(
                    $request->tripsForHighlights()
                        ->highlights()
                        ->get()
                ),
                'trips'
            ),
            'countries' => array_map(
                fn($country) => $country['name'],
                (new CountriesHelper())->list()
            ),
        ];

        return inertia('Map', $props);
    }
}
