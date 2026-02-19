<?php

declare(strict_types=1);

namespace App\Subscription\Application\Command\Sync;

use App\Shared\Application\Command\Sync\Command;

final readonly class CreatePortalSession implements Command
{
    public function __construct(
        public string $ownerId,
    ) {}
}
