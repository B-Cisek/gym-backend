<?php

declare(strict_types=1);

namespace App\Gym\Application\Command\Sync;

use App\Shared\Application\Command\Sync\Command;

final readonly class CreateGym implements Command
{
    public function __construct(
        public string $ownerId,
        public string $name,
        public string $street,
        public string $city,
        public string $postalCode,
        public string $voivodeship,
    ) {}
}
