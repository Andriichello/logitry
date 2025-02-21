<?php

namespace App\Http\Controllers\Web;

use App\Helpers\BoundsHelper;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Web\MapRequest;
use App\Http\Resources\Specific\CompanyResource;
use App\Http\Resources\Specific\RouteResource;
use App\Http\Responses\ApiResponse;
use App\Models\Route;
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
        $query = Route::query();

        $company = $request->company();

        if ($company) {
            $query->where('company_id', $company->id);
        }

        $beg = $request->beg();
        $end = $request->end();

        if ($beg || $end) {
            $query->tripsDepartWithin($beg, $end);
        }

        $routes = $query->get()->all();

        $props = [
            'company' => ($company = $request->company())
                ? new CompanyResource($company) : null,
            'bounds' => (new BoundsHelper())
                ->forRoutes($routes),
            'routes' => RouteResource::collection($routes),
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
        $query = Route::query();

        $company = $request->company();

        if ($company) {
            $query->where('company_id', $company->id);
        }

        $beg = $request->beg();
        $end = $request->end();

        if ($beg || $end) {
            $query->tripsDepartWithin($beg, $end);
        }

        $routes = $query->get();

        $props = [
            'company' => ($company = $request->company())
                ? new CompanyResource($company) : null,
            'routes' => RouteResource::collection($routes),
            'bounds' => (new BoundsHelper())
                ->forRoutes($routes->all()),
        ];

        return inertia('Map', $props);
    }
}
