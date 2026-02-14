<?php

declare(strict_types=1);

namespace App\Gym\Application\Query\Result;

final readonly class GymCollection
{
    /** @param Gym[] $gyms */
    public function __construct(public array $gyms) {}
}
