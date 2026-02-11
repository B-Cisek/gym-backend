<?php

declare(strict_types=1);

namespace App\Gym\Application\Command\Sync;

use App\Shared\Application\Command\Sync\Command;

final readonly class DeleteGym implements Command
{
    public function __construct(
        public string $gymId,
        public string $ownerId,
    ) {}
}
