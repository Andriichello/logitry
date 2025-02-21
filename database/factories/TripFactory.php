<?php

namespace Database\Factories;

use App\Enum\TripStatus;
use App\Models\Contact;
use App\Models\Route;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class TripFactory.
 *
 * @extends Factory<Trip>
 */
class TripFactory extends Factory
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
            'reversed' => $this->faker->boolean(),
            'vehicle_id' => null,
            'driver_id' => null,
            'contact_id' => null,
            'status' => $this->faker->randomElement(TripStatus::cases()),
            'departs_at' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
            'arrives_at' => $this->faker->optional()->dateTimeBetween('+1 day', '+2 months'),
            'metadata' => null,

        ];
    }

    /**
     * Indicate route.
     *
     * @param Route $route
     *
     * @return static
     */
    public function withRoute(Route $route): static
    {
        return $this->state(
            function (array $attributes) use ($route) {
                $attributes['route_id'] = $route->id;

                return $attributes;
            }
        );
    }

    /**
     * Indicate vehicle.
     *
     * @param Vehicle|null $vehicle
     *
     * @return static
     */
    public function withVehicle(?Vehicle $vehicle): static
    {
        return $this->state(
            function (array $attributes) use ($vehicle) {
                $attributes['vehicle_id'] = $vehicle?->id;

                return $attributes;
            }
        );
    }

    /**
     * Indicate driver.
     *
     * @param User|null $driver
     *
     * @return static
     */
    public function withDriver(?User $driver): static
    {
        return $this->state(
            function (array $attributes) use ($driver) {
                $attributes['driver_id'] = $driver?->id;

                return $attributes;
            }
        );
    }

    /**
     * Indicate contact.
     *
     * @param Contact|null $contact
     *
     * @return static
     */
    public function withContact(?Contact $contact): static
    {
        return $this->state(
            function (array $attributes) use ($contact) {
                $attributes['contact_id'] = $contact?->id;

                return $attributes;
            }
        );
    }
}
