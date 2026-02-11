<?php

declare(strict_types=1);

namespace App\Owner\Presentation\Http\Response\V1;

final readonly class OwnerProfileResponse
{
    public function __construct(
        public string $id,
        public string $userId,
        public string $companyName,
        public ?string $taxId,
        public ?string $phone,
        public ?string $street,
        public ?string $city,
        public ?string $postalCode,
        public ?string $country,
    ) {}
}
