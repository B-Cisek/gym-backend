<?php

declare(strict_types=1);

namespace App\Gym\Application\Query\Result;

final readonly class Gym
{
    public function __construct(
        public string $id,
        public string $name,
        public \DateTimeImmutable $createdAt,
        public ?string $street = null,
        public ?string $city = null,
        public ?string $postalCode = null,
    ) {}
}
