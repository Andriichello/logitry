<?php

namespace App\Helpers;

use App\Helpers\Interfaces\BoundsHelperInterface;
use App\Models\Point;
use App\Models\Route;
use Illuminate\Support\Collection;

/**
 * Class BoundsHelper.
 */
class BoundsHelper implements BoundsHelperInterface
{
    /**
     * Calculate bounds for the given points.
     *
     * @param Collection|Point[] $points
     *
     * @return null|array{
     *     northEast: array{
     *         latitude: float,
     *         longitude: float,
     *     },
     *     southWest: array{
     *          latitude: float,
     *          longitude: float,
     *     },
     * }
     */
    public function forPoints(Collection|array $points): ?array
    {
        $latitudes = [];
        $longitudes = [];

        foreach ($points as $point) {
            $latitudes[] = $point->latitude;
            $longitudes[] = $point->longitude;
        }

        if (empty($latitudes) || empty($longitudes)) {
            return null;
        }

        return [
            'northEast' => [
                'latitude' => max($latitudes),
                'longitude' => max($longitudes),
            ],
            'southWest' => [
                'latitude' => min($latitudes),
                'longitude' => min($longitudes),
            ],
        ];
    }

    /**
     * Calculate bounds for the given points.
     *
     * @param Route $route
     *
     * @return null|array{
     *     northEast: array{
     *         latitude: float,
     *         longitude: float,
     *     },
     *     southWest: array{
     *          latitude: float,
     *          longitude: float,
     *     },
     * }
     */
    public function forRoute(Route $route): ?array
    {
        return $this->forPoints($route->points->all());
    }

    /**
     * Calculate bounds for the given routes.
     *
     * @param Collection|Route[] $routes
     *
     * @return null|array{
     *     northEast: array{
     *         latitude: float,
     *         longitude: float,
     *     },
     *     southWest: array{
     *          latitude: float,
     *          longitude: float,
     *     },
     * }
     */
    public function forRoutes(Collection|array $routes): ?array
    {
        $points = [];

        foreach ($routes as $route) {
            $points = array_merge($points, $route->points->all());
        }

        return $this->forPoints($routes);
    }
}
