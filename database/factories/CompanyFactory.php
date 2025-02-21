<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class CompanyFactory.
 *
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->title(),
        ];
    }

    /**
     * Indicate company.
     *
     * @param Company|null $company
     *
     * @return static
     */
    public function withParent(?Company $company): static
    {
        return $this->state(
            function (array $attributes) use ($company) {
                $attributes['parent_id'] = $company?->id;

                return $attributes;
            }
        );
    }
}
