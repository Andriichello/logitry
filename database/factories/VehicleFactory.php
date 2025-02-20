<?php

namespace Database\Factories;

use App\Enum\Fuel;
use App\Enum\VehicleType;
use App\Models\Company;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class VehicleFactory.
 *
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'manufacturer' => $this->faker->company(),
            'model' => $this->faker->word(),
            'year' => $this->faker->year(),
            'color' => $this->faker->safeColorName(),
            'nickname' => $this->faker->optional()->word(),
            'description' => $this->faker->optional()->sentence(),
            'type' => $this->faker->randomElement(VehicleType::cases()),
            'fuel' => $this->faker->randomElement(Fuel::cases()),
            'seats' => $this->faker->numberBetween(2, 60),
            'cargo_width' => $this->faker->optional()
                ->randomFloat(2, 1.0, 3.0),
            'cargo_length' => $this->faker->optional()
                ->randomFloat(2, 2.0, 12.0),
            'cargo_height' => $this->faker->optional()
                ->randomFloat(2, 1.0, 4.0),
            'cargo_volume' => $this->faker->optional()
                ->randomFloat(2, 5.0, 100.0),
            'cargo_capacity' => $this->faker->optional()
                ->numberBetween(500, 50000),
            'metadata' => null,
        ];
    }

    /** Indicate company.
     *
     * @param Company|null $company
     *
     * @return static
     */
    public function withCompany(?Company $company): static
    {
        return $this->state(
            function (array $attributes) use ($company) {
                $attributes['company_id'] = $company?->id;

                return $attributes;
            }
        );
    }
}
