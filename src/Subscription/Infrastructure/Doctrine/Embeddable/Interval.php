<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Doctrine\Embeddable;

use App\Subscription\Domain\IntervalUnit;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;

#[Embeddable]
readonly class Interval
{
    public function __construct(
        #[Column(type: Types::INTEGER)]
        private int $value,
        #[Column(enumType: IntervalUnit::class)]
        private IntervalUnit $unit,
    )
    {
    }

    public function getValue(): int
    {
        return $this->value;

    }

    public function getUnit(): IntervalUnit
    {
        return $this->unit;
    }

    public function toSeconds(): int
    {
        return $this->value * $this->unit->toSeconds();
    }
}
