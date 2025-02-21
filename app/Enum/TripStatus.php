<?php

namespace App\Enum;

/**
 * Enum Unit.
 */
enum TripStatus: string
{
    case Draft = 'Draft';
    case Available = 'Available';
    case Unavailable = 'Unavailable';

    case InProgress = 'In Progress';
    case Finished = 'Finished';
    case Cancelled = 'Cancelled';
}
