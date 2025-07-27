<?php

namespace Database\Seeders;

use App\Enum\Realm;
use App\Enum\Role;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Point;
use App\Models\Route;
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

        // Seed routes for Das Auto
        // Route 1: Uzhhorod - Košice - Prešov
        $dasAutoRoute1 = Route::factory()
            ->withCompany($dasAuto)
            ->create([
                'name' => 'Uzhhorod - Košice - Prešov',
                'description' => 'Route from Uzhhorod through Košice to Prešov',
            ]);

        // Add points to Route 1
        Point::factory()
            ->withRoute($dasAutoRoute1)
            ->create([
                'number' => 1,
                'name' => 'Uzhhorod',
                'country' => 'UA',
                'city' => 'Uzhhorod',
                'latitude' => 48.6208,
                'longitude' => 22.2879,
            ]);

        Point::factory()
            ->withRoute($dasAutoRoute1)
            ->create([
                'number' => 2,
                'name' => 'Košice',
                'country' => 'Slovakia',
                'city' => 'Košice',
                'latitude' => 48.7164,
                'longitude' => 21.2611,
            ]);

        Point::factory()
            ->withRoute($dasAutoRoute1)
            ->create([
                'number' => 3,
                'name' => 'Prešov',
                'country' => 'Slovakia',
                'city' => 'Prešov',
                'latitude' => 49.0018,
                'longitude' => 21.2393,
            ]);

        // Route 2: Uzhhorod - Kisvárda - Nyíregyháza
        $dasAutoRoute2 = Route::factory()
            ->withCompany($dasAuto)
            ->create([
                'name' => 'Uzhhorod - Kisvárda - Nyíregyháza',
                'description' => 'Route from Uzhhorod through Kisvárda to Nyíregyháza',
            ]);

        // Add points to Route 2
        Point::factory()
            ->withRoute($dasAutoRoute2)
            ->create([
                'number' => 1,
                'name' => 'Uzhhorod',
                'country' => 'UA',
                'city' => 'Uzhhorod',
                'latitude' => 48.6208,
                'longitude' => 22.2879,
            ]);

        Point::factory()
            ->withRoute($dasAutoRoute2)
            ->create([
                'number' => 2,
                'name' => 'Kisvárda',
                'country' => 'HU',
                'city' => 'Kisvárda',
                'latitude' => 48.2174,
                'longitude' => 22.0818,
            ]);

        Point::factory()
            ->withRoute($dasAutoRoute2)
            ->create([
                'number' => 3,
                'name' => 'Nyíregyháza',
                'country' => 'HU',
                'city' => 'Nyíregyháza',
                'latitude' => 47.9495,
                'longitude' => 21.7244,
            ]);

        // Seed routes for Das Auto (Terry)
        // Route 1: Prague - Brno
        $dasAutoTerryRoute1 = Route::factory()
            ->withCompany($dasAutoTerry)
            ->create([
                'name' => 'Prague - Brno',
                'description' => 'Route from Prague to Brno',
            ]);

        // Add points to Route 1
        Point::factory()
            ->withRoute($dasAutoTerryRoute1)
            ->create([
                'number' => 1,
                'name' => 'Prague',
                'country' => 'CZ',
                'city' => 'Prague',
                'latitude' => 50.0755,
                'longitude' => 14.4378,
            ]);

        Point::factory()
            ->withRoute($dasAutoTerryRoute1)
            ->create([
                'number' => 2,
                'name' => 'Brno',
                'country' => 'CZ',
                'city' => 'Brno',
                'latitude' => 49.1951,
                'longitude' => 16.6068,
            ]);

        // Route 2: Berlin - Munich
        $dasAutoTerryRoute2 = Route::factory()
            ->withCompany($dasAutoTerry)
            ->create([
                'name' => 'Berlin - Munich',
                'description' => 'Route from Berlin to Munich',
            ]);

        // Add points to Route 2
        Point::factory()
            ->withRoute($dasAutoTerryRoute2)
            ->create([
                'number' => 1,
                'name' => 'Berlin',
                'country' => 'DE',
                'city' => 'Berlin',
                'latitude' => 52.5200,
                'longitude' => 13.4050,
            ]);

        Point::factory()
            ->withRoute($dasAutoTerryRoute2)
            ->create([
                'number' => 2,
                'name' => 'Munich',
                'country' => 'DE',
                'city' => 'Munich',
                'latitude' => 48.1351,
                'longitude' => 11.5820,
            ]);

        // Seed routes for Haul Auto
        // Route 1: Warsaw - Krakow
        $haulAutoRoute1 = Route::factory()
            ->withCompany($haulAuto)
            ->create([
                'name' => 'Warsaw - Krakow',
                'description' => 'Route from Warsaw to Krakow',
            ]);

        // Add points to Route 1
        Point::factory()
            ->withRoute($haulAutoRoute1)
            ->create([
                'number' => 1,
                'name' => 'Warsaw',
                'country' => 'PL',
                'city' => 'Warsaw',
                'latitude' => 52.2297,
                'longitude' => 21.0122,
            ]);

        Point::factory()
            ->withRoute($haulAutoRoute1)
            ->create([
                'number' => 2,
                'name' => 'Krakow',
                'country' => 'PL',
                'city' => 'Krakow',
                'latitude' => 50.0647,
                'longitude' => 19.9450,
            ]);

        // Route 2: Paris - Lyon
        $haulAutoRoute2 = Route::factory()
            ->withCompany($haulAuto)
            ->create([
                'name' => 'Paris - Lyon',
                'description' => 'Route from Paris to Lyon',
            ]);

        // Add points to Route 2
        Point::factory()
            ->withRoute($haulAutoRoute2)
            ->create([
                'number' => 1,
                'name' => 'Paris',
                'country' => 'FR',
                'city' => 'Paris',
                'latitude' => 48.8566,
                'longitude' => 2.3522,
            ]);

        Point::factory()
            ->withRoute($haulAutoRoute2)
            ->create([
                'number' => 2,
                'name' => 'Lyon',
                'country' => 'FR',
                'city' => 'Lyon',
                'latitude' => 45.7640,
                'longitude' => 4.8357,
            ]);

        // Route 3: Amsterdam - Brussels - Paris (cross-country route)
        $haulAutoRoute3 = Route::factory()
            ->withCompany($haulAuto)
            ->create([
                'name' => 'Amsterdam - Brussels - Paris',
                'description' => 'Route from Amsterdam through Brussels to Paris',
            ]);

        // Add points to Route 3
        Point::factory()
            ->withRoute($haulAutoRoute3)
            ->create([
                'number' => 1,
                'name' => 'Amsterdam',
                'country' => 'NL',
                'city' => 'Amsterdam',
                'latitude' => 52.3676,
                'longitude' => 4.9041,
            ]);

        Point::factory()
            ->withRoute($haulAutoRoute3)
            ->create([
                'number' => 2,
                'name' => 'Brussels',
                'country' => 'BE',
                'city' => 'Brussels',
                'latitude' => 50.8503,
                'longitude' => 4.3517,
            ]);

        Point::factory()
            ->withRoute($haulAutoRoute3)
            ->create([
                'number' => 3,
                'name' => 'Paris',
                'country' => 'FR',
                'city' => 'Paris',
                'latitude' => 48.8566,
                'longitude' => 2.3522,
            ]);
    }
}
