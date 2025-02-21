<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Route;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class RouteFactory.
 *
 * @extends Factory<Route>
 */
class RouteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => null,
            'contact_id' => null,
            'vehicle_id' => null,
            'driver_id' => null,
            'name' => $this->faker->optional()->word(),
            'description' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Indicate company.
     *
     * @param Company $company
     *
     * @return static
     */
    public function withCompany(Company $company): static
    {
        return $this->state(
            function (array $attributes) use ($company) {
                $attributes['company_id'] = $company->id;

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
}
