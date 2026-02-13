<?php

declare(strict_types=1);

namespace App\Owner\Application\Query\Result;

final readonly class OwnerSettings
{
    public function __construct(
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $companyName = null,
        public ?string $taxId = null,
        public ?string $street = null,
        public ?string $city = null,
        public ?string $postalCode = null,
    )
    {
    }
}
