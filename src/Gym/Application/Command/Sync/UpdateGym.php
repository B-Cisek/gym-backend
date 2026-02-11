<?php

declare(strict_types=1);

namespace App\Gym\Application\Command\Sync;

use App\Shared\Application\Command\Sync\Command;

final readonly class UpdateGym implements Command
{
    public function __construct(
        public string $gymId,
        public string $ownerId,
        public string $name,
        public ?string $street = null,
        public ?string $city = null,
        public ?string $postalCode = null,
        public ?string $voivodeship = null,
    ) {}
}
