<?php

namespace App\Enum;

/**
 * Enum Fuel.
 */
enum Fuel: string
{
    case Petrol = 'Petrol';
    case Diesel = 'Diesel';
    case Electric = 'Electric';
    case Hybrid = 'Hybrid';
    case Gas = 'Gas';
}
