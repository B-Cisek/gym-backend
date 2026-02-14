<?php

declare(strict_types=1);

namespace App\Gym\Application\Query\Result;

final readonly class GymMenu
{
    public function __construct(
        public string $id,
        public string $name,
    ) {}
}
