<?php

namespace App\Enum;

/**
 * Enum Role.
 */
enum Role: string
{
    case Owner = 'Owner';
    case Admin = 'Admin';
    case Manager = 'Manager';
}
