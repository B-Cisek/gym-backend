<?php

declare(strict_types=1);

namespace App\Gym\Presentation\Http\Request\V1;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

final readonly class CreateGymRequest
{
    public function __construct(
        #[NotBlank]
        #[Length(min: 2, max: 255)]
        public string $name,
        #[Length(max: 255)]
        public ?string $street = null,
        #[Length(max: 20)]
        public ?string $city = null,
        #[Length(exactly: 6)]
        #[Regex(pattern: '/^[0-9]{2}-[0-9]{3}$/', message: 'Invalid postal code format, eg. XX-XXX)')]
        public ?string $postalCode = null,
    ) {}
}
