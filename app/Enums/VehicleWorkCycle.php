<?php

namespace App\Enums;

enum VehicleWorkCycle: int
{
    case OneTime = 1;
    case EveryWeek = 2;
    case EveryMonth = 3;
    case EveryYear = 4;

    /**
     * Get human-readable label for the work cycle.
     */
    public function label(): string
    {
        return match ($this) {
            self::OneTime => 'One Time',
            self::EveryWeek => 'Every week',
            self::EveryMonth => 'Every month',
            self::EveryYear => 'Every Year',
        };
    }
}
