<?php

declare(strict_types=1);

namespace App\Gym\Presentation\Http\Request\V1;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final readonly class UpdateGymRequest
{
    public function __construct(
        #[NotBlank]
        #[Length(max: 255)]
        public string $name,
        #[Length(max: 255)]
        public ?string $street = null,
        #[Length(max: 20)]
        public ?string $city = null,
        #[Length(exactly: 6)]
        public ?string $postalCode = null,
        #[Length(max: 100)]
        public ?string $voivodeship = null,
    ) {}
}
