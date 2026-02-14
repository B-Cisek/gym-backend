<?php

declare(strict_types=1);

namespace App\Shared\Domain;

final readonly class Address
{
    // null ??
    public function __construct(
        public ?string $street = null,
        public ?string $city = null,
        public ?string $postalCode = null,
    ) {}
}
