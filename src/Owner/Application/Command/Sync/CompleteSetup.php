<?php

declare(strict_types=1);

namespace App\Owner\Application\Command\Sync;

use App\Shared\Application\Command\Sync\Command;

final readonly class CompleteSetup implements Command
{
    public function __construct(public string $ownerId) {}
}
