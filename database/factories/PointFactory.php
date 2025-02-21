<?php

namespace Database\Factories;

use App\Models\Point;
use App\Models\Route;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class PointFactory.
 *
 * @extends Factory<Point>
 */
class PointFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'route_id' => Route::factory(),
            'number' => $this->faker->unique()->numberBetween(1, 1000),
            'name' => $this->faker->word(),
            'description' => $this->faker->optional()->sentence(),
            'country' => $this->faker->country(),
            'city' => $this->faker->city(),
            'street' => $this->faker->streetAddress(),
            'latitude' => $this->faker->latitude(35, 70),
            'longitude' => $this->faker->longitude(-10, 40),
            'travel_time' => $this->faker->optional()->numberBetween(1, 1440),
            'travel_time_cap' => $this->faker->optional()->numberBetween(1, 1440),
        ];
    }

    /**
     * Indicate route.
     *
     * @param Route|null $route
     *
     * @return static
     */
    public function withRoute(?Route $route): static
    {
        return $this->state(
            function (array $attributes) use ($route) {
                $attributes['route_id'] = $route?->id;

                return $attributes;
            }
        );
    }
}
