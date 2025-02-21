<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Route;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class ContactFactory.
 *
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
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
            'country' => $this->faker->optional()->country(),
            'city' => $this->faker->optional()->city(),
            'street' => $this->faker->optional()->streetAddress(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
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
