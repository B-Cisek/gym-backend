<?php

declare(strict_types=1);

namespace App\Owner\Application\Command\Sync;

use App\Shared\Application\Command\Sync\Command;

final readonly class CreateOwner implements Command
{
    public function __construct(public string $userId) {}
}
