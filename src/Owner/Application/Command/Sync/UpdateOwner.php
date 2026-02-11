<?php

declare(strict_types=1);

namespace App\Owner\Application\Command\Sync;

use App\Shared\Application\Command\Sync\Command;

final readonly class UpdateOwner implements Command
{
    public function __construct(
        public string $userId,
        public ?string $companyName = null,
        public ?string $taxId = null,
        public ?string $phone = null,
        public ?string $street = null,
        public ?string $city = null,
        public ?string $postalCode = null,
    ) {}
}
