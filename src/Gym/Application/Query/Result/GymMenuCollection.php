<?php

declare(strict_types=1);

namespace App\Gym\Application\Query\Result;

final readonly class GymMenuCollection
{
    /** @param GymMenu[] $gyms */
    public function __construct(public array $gyms) {}
}
