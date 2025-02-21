<?php

namespace Database\Factories;

use App\Enum\Role;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class CompanyUserFactory.
 *
 * @extends Factory<CompanyUser>
 */
class CompanyUserFactory extends Factory
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
            'user_id' => User::factory(),
            'role' => Role::Admin,
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

    /** Indicate user.
     *
     * @param User $user
     *
     * @return static
     */
    public function withUser(User $user): static
    {
        return $this->state(
            function (array $attributes) use ($user) {
                $attributes['user_id'] = $user->id;

                return $attributes;
            }
        );
    }

    /** Indicate role.
     *
     * @param Role $role
     *
     * @return static
     */
    public function withRole(Role $role): static
    {
        return $this->state(
            function (array $attributes) use ($role) {
                $attributes['role'] = $role;

                return $attributes;
            }
        );
    }
}
