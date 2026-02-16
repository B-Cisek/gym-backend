<?php

declare(strict_types=1);

namespace App\Subscription\Domain;

enum IntervalUnit: string
{
    case DAY = 'day';

    case WEEK = 'week';

    case MONTH = 'month';

    case YEAR = 'year';

    public function toSeconds(): int
    {
        return match ($this) {
            IntervalUnit::DAY => 24 * 60 * 60,
            IntervalUnit::WEEK => 7 * 24 * 60 * 60,
            IntervalUnit::MONTH => 30 * 24 * 60 * 60,
            IntervalUnit::YEAR => 365 * 24 * 60 * 60,
        };
    }
}
