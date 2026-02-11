<?php

declare(strict_types=1);

namespace App\Owner\Presentation\Http\Request\V1;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final readonly class UpdateOwnerProfileRequest
{
    public function __construct(
        #[NotBlank]
        #[Length(max: 255)]
        public string $companyName,
        #[Length(max: 50)]
        public ?string $taxId = null,
        #[Length(max: 50)]
        public ?string $phone = null,
        #[Length(max: 255)]
        public ?string $street = null,
        #[Length(max: 100)]
        public ?string $city = null,
        #[Length(max: 20)]
        public ?string $postalCode = null,
        #[Length(max: 100)]
        public ?string $voivodeship = null,
    ) {}
}
