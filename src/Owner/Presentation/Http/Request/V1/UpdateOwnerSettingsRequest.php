<?php

declare(strict_types=1);

namespace App\Owner\Presentation\Http\Request\V1;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

final readonly class UpdateOwnerSettingsRequest
{
    public function __construct(
        #[Length(max: 100)]
        public ?string $firstName = null,
        #[Length(max: 100)]
        public ?string $lastName = null,
        #[Email]
        #[Length(max: 255)]
        public ?string $email = null,
        #[Length(max: 255)]
        public ?string $companyName = null,
        #[Length(max: 10)]
        public ?string $taxId = null,
        #[Length(max: 50)]
        public ?string $phone = null,
        #[Length(max: 255)]
        public ?string $street = null,
        #[Length(max: 20)]
        public ?string $postalCode = null,
        #[Length(max: 100)]
        public ?string $city = null,
    ) {}
}
