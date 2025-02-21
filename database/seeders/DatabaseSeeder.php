<?php

namespace Database\Seeders;

use App\Enum\Realm;
use App\Enum\Role;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Point;
use App\Models\Route;
use App\Models\User;
use App\Models\Vehicle;
use Database\Factories\PointFactory;
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

        $mike = User::factory()
            ->create([
                'name' => 'Mike',
                'email' => 'mike@email.com',
                'phone' => '+380991236819',
                'password' => 'pa$$w0rd',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]);

        CompanyUser::factory()
            ->withCompany($dasAuto)
            ->withUser($mike)
            ->withRole(Role::Driver)
            ->create();

        $routeOne = Route::factory()
            ->withCompany($dasAuto)
            ->withVehicle(Vehicle::factory()->create())
            ->withDriver($mike)
            ->create(['name' => 'Route One']);

        $pointsOne = Point::factory()
            ->withRoute($routeOne)
            ->count(4)
            ->create();

        $routeTwo = Route::factory()
            ->withCompany($dasAuto)
            ->withVehicle(Vehicle::factory()->create())
            ->create(['name' => 'Route Two']);

        $pointsTwo = Point::factory()
            ->withRoute($routeTwo)
            ->count(4)
            ->create();

        $routeThree = Route::factory()
            ->withCompany($dasAuto)
            ->withVehicle(Vehicle::factory()->create())
            ->create(['name' => 'Route Three']);

        $pointsThree = Point::factory()
            ->withRoute($routeThree)
            ->count(4)
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
                'name' => 'Haul Auto',
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

        $james = User::factory()
            ->create([
                'name' => 'James',
                'email' => 'james@email.com',
                'phone' => '+380991232056',
                'password' => 'pa$$w0rd',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]);

        CompanyUser::factory()
            ->withCompany($haulAuto)
            ->withUser($james)
            ->withRole(Role::Driver)
            ->create();

        $peter = User::factory()
            ->create([
                'name' => 'Peter',
                'email' => 'peter@email.com',
                'phone' => '+380991231602',
                'password' => 'pa$$w0rd',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]);

        CompanyUser::factory()
            ->withCompany($haulAuto)
            ->withUser($peter)
            ->withRole(Role::Driver)
            ->create();

        CompanyUser::factory()
            ->withCompany($haulAuto)
            ->withUser($peter)
            ->withRole(Role::Driver)
            ->create();
    }
}
