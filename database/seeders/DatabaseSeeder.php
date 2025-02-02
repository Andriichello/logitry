<?php

namespace Database\Seeders;

use App\Enum\Realm;
use App\Enum\Role;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Class DatabaseSeeder.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $dasAuto = Company::factory()
            ->create([
                'abbreviation' => 'das-auto',
                'name' => 'Das Auto',
                'realm' => Realm::Logistics,
                'plan' => 'Free',
            ]);

        $john = User::factory()
            ->create([
                'name' => 'John',
                'email' => 'john@email.com',
                'phone' => '+380991234567',
                'password' => 'pa$$w0rd',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]);

        CompanyUser::factory()
            ->withCompany($dasAuto)
            ->withUser($john)
            ->withRole(Role::Owner)
            ->create();

        $dasAutoTerry = Company::factory()
            ->create([
                'parent_id' => $dasAuto->id,
                'abbreviation' => 'das-auto-terry',
                'name' => 'Das Auto (Terry)',
                'realm' => Realm::Logistics,
                'plan' => null,
            ]);

        $terry = User::factory()
            ->create([
                'name' => 'Terry',
                'email' => 'terry@email.com',
                'phone' => '+380991239876',
                'password' => 'pa$$w0rd',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]);

        CompanyUser::factory()
            ->withCompany($dasAutoTerry)
            ->withUser($terry)
            ->withRole(Role::Admin)
            ->create();

        $haulAuto = Company::factory()
            ->create([
                'abbreviation' => 'haul-auto',
                'name' => 'Haul Auto (Sub)',
                'realm' => Realm::Logistics,
                'plan' => 'Free',
            ]);

        $paul = User::factory()
            ->create([
                'name' => 'Paul',
                'email' => 'paul@email.com',
                'phone' => '+380991238765',
                'password' => 'pa$$w0rd',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]);

        CompanyUser::factory()
            ->withCompany($haulAuto)
            ->withUser($paul)
            ->withRole(Role::Owner)
            ->create();
    }
}
