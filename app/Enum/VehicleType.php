<?php

namespace App\Enum;

/**
 * Enum VehicleType.
 */
enum VehicleType: string
{
    case Sedan = 'Sedan';
    case Hatchback = 'Hatchback';
    case Coupe = 'Coupe';
    case Convertible = 'Convertible';
    case StationWagon = 'Station Wagon';
    case SUV = 'SUV';
    case Crossover = 'Crossover';
    case Minivan = 'Minivan';
    case Van = 'Van';
    case Truck = 'Truck';
    case PickupTruck = 'Pickup Truck';
    case BoxTruck = 'Box Truck';
    case FlatbedTruck = 'Flatbed Truck';
    case SemiTruck = 'Semi-Truck';
}
