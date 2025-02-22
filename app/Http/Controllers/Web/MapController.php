<?php

namespace App\Http\Controllers\Web;

use App\Helpers\BoundsHelper;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Web\MapRequest;
use App\Http\Resources\Specific\CompanyResource;
use App\Http\Resources\Specific\RouteResource;
use App\Http\Resources\Specific\TripResource;
use App\Http\Responses\ApiResponse;
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
     * @return ApiResponse
     * @SuppressWarnings(PHPMD)
     */
    public function get(MapRequest $request): ApiResponse
    {
        $routes = $request->routes()->get();
        $trips = $request->trips()->get();

        $props = [
            'company' => ($company = $request->company())
                ? new CompanyResource($company) : null,
            'bounds' => (new BoundsHelper())
                ->forRoutes($routes->all()),
            'routes' => RouteResource::collection($routes),
            'trips' => TripResource::collection($trips),
        ];

        return ApiResponse::ok($props);
    }


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
        $routes = $request->routes()->get();

        $props = [
            'company' => ($company = $request->company())
                ? new CompanyResource($company) : null,
            'routes' => RouteResource::collection($routes),
            'bounds' => (new BoundsHelper())
                ->forRoutes($routes->all()),
            'trips' => Inertia::defer(
                fn() => TripResource::collection($request->trips()->get())
            ),
        ];

        return inertia('Map', $props);
    }
}
