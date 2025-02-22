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
     * @param float|null $padding [0 ; 1]
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
    public function forPoints(Collection|array $points, ?float $padding = null): ?array
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

        $latPadding = $padding
            ? (max($latitudes) - min($latitudes)) * $padding : null;
        $longPadding = $padding
            ? (max($longitudes) - min($longitudes)) * $padding : null;

        return [
            'northEast' => [
                'latitude' => max($latitudes) + $latPadding,
                'longitude' => max($longitudes) + $longPadding,
            ],
            'southWest' => [
                'latitude' => min($latitudes) - $latPadding,
                'longitude' => min($longitudes) - $longPadding,
            ],
        ];
    }

    /**
     * Calculate bounds for the given points.
     *
     * @param Route $route
     * @param float|null $padding [0 ; 1]
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
    public function forRoute(Route $route, ?float $padding = null): ?array
    {
        return $this->forPoints($route->points->all(), $padding);
    }

    /**
     * Calculate bounds for the given routes.
     *
     * @param Collection|Route[] $routes
     * @param float|null $padding [0 ; 1]
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
    public function forRoutes(Collection|array $routes, ?float $padding = null): ?array
    {
        $points = [];

        foreach ($routes as $route) {
            $points = array_merge($points, $route->points->all());
        }

        return $this->forPoints($points, $padding);
    }
}
