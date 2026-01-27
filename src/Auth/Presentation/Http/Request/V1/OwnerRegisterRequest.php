<?php

declare(strict_types=1);

namespace App\Auth\Presentation\Http\Request\V1;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final readonly class OwnerRegisterRequest
{
    public function __construct(
        #[NotBlank]
        #[Email]
        public string $email,
        #[NotBlank]
        #[Length(min: 8, max: 180)]
        #[\SensitiveParameter]
        public string $password,
    ) {}
}
