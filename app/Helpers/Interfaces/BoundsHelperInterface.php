<?php

namespace App\Helpers\Interfaces;

use App\Models\Point;
use App\Models\Route;
use Illuminate\Support\Collection;

/**
 * Interface BoundsHelperInterface.
 */
interface BoundsHelperInterface
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
    public function forPoints(Collection|array $points): ?array;

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
    public function forRoute(Route $route): ?array;

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
    public function forRoutes(Collection|array $routes): ?array;
}
