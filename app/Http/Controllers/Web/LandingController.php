<?php

namespace App\Http\Controllers\Web;

use App\Helpers\BoundsHelper;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Web\LandingRequest;
use App\Http\Resources\Specific\CompanyResource;
use App\Http\Resources\Specific\RouteResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Inertia\ResponseFactory;

/**
 * Class MapController.
 */
class LandingController extends BaseController
{
    /**
     * Returns landing data.
     *
     * @param LandingRequest $request
     *
     * @return ApiResponse|RedirectResponse
     * @SuppressWarnings(PHPMD)
     */
    public function get(LandingRequest $request): ApiResponse|RedirectResponse
    {
        $props = [
            'company' => ($company = $request->company())
                ? new CompanyResource($company) : null,
            'routes' => null,
            'bounds' => null,
        ];

        if ($company === null) {
            $props['errors'] = [
                'company' => 'No such company found.',
            ];

            return ApiResponse::ok($props);
        }

        $routes = $request->routes()
            ->limit(20)
            ->get();

        $props['routes'] = RouteResource::collection($routes);
        $props['bounds'] = (new BoundsHelper())
            ->forRoutes($routes->all(), 0.4);

        return ApiResponse::ok($props);
    }


    /**
     * Returns landing view.
     *
     * @param LandingRequest $request
     *
     * @return RedirectResponse|Response|ResponseFactory
     * @SuppressWarnings(PHPMD)
     */
    public function view(LandingRequest $request): RedirectResponse|Response|ResponseFactory
    {
        $props = [
            'company' => ($company = $request->company())
                ? new CompanyResource($company) : null,
            'routes' => [],
        ];

        if ($company === null) {
            $props['errors'] = [
                'company' => 'No such company found.',
            ];

            return inertia('Landing', $props);
        }

        $routes = $request->routes()
            ->limit(20)
            ->get();

        $props['routes'] = RouteResource::collection($routes);
        $props['bounds'] = (new BoundsHelper())
            ->forRoutes($routes->all(), 1.2);

        return inertia('Landing', $props);
    }
}
