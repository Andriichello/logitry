<?php

namespace App\Http\Controllers\Web;

use App\Helpers\BoundsHelper;
use App\Helpers\CountriesHelper;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Traits\WithPagination;
use App\Http\Requests\Web\MapRequest;
use App\Http\Resources\ResourcePaginator;
use App\Http\Resources\Specific\CompanyResource;
use App\Http\Resources\Specific\RouteResource;
use App\Http\Resources\Specific\TripHighlightResource;
use App\Http\Resources\Specific\TripResource;
use App\Http\Responses\ApiResponse;
use App\Models\Route;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Inertia\ResponseFactory;

/**
 * Class MapController.
 *
 * @SuppressWarnings(PHPMD)
 */
class MapController extends BaseController
{
    use WithPagination;

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
                'filters' => $request->filters(),
                'selections' => $request->selections(),
                'routes' => [],
                'bounds' => null,
                'trips' => null,
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
                        'abbreviation' => $request->abbreviation(),
                    ]
                );
        }

        $routes = new ResourcePaginator(
            $this->paginate($request->routes()),
            RouteResource::class,
        );

        $trips = $request->trips()->get();

        $tripHighlights = $request->tripsForHighlights()
            ->highlights()
            ->get();

        $props = [
            'company' => ($company = $request->company())
                ? new CompanyResource($company) : null,
            'filters' => $request->filters(),
            'selections' => $request->selections(),
            'routes' => RouteResource::collection($routes),
            'bounds' => (new BoundsHelper())
                ->forRoutes($routes->all(), 0.05),
            'trips' => TripResource::collection($trips),
            'trip_highlights' => TripHighlightResource::collection($tripHighlights),
            'meta' => $routes->meta(),
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
                'filters' => $request->filters(),
                'selections' => $request->selections(),
                'routes' => [],
                'bounds' => null,
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
                        'abbreviation' => $request->abbreviation(),
                    ]
                );
        }

        $routes = new ResourcePaginator(
            $this->paginate($request->routes()),
            RouteResource::class,
        );

        $props = [
            'company' => ($company = $request->company())
                ? new CompanyResource($company) : null,
            'filters' => $request->filters(),
            'selections' => $request->selections(),
            'routes' => Inertia::merge(RouteResource::collection($routes->all())),
            'bounds' => (new BoundsHelper())
                ->forRoutes($routes->all(), 0.05),
//            'trips' => Inertia::defer(
//                function() use($request) {
//                    sleep(5);
//
//                    return TripResource::collection($request->trips()->get());
//                },
//                'trips'
//            ),
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
            'meta' => $routes->meta(),
        ];

        return inertia('Map', $props);
    }

    /**
     * Get the default size of the page for pagination.
     *
     * @return int
     */
    public function getDefaultPageSize(): int
    {
        return 100;
    }
}
