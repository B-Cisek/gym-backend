<?php

declare(strict_types=1);

namespace App\Subscription\Presentation\Http\Request\V1;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

final readonly class CreateCheckoutSessionRequest
{
    public function __construct(
        #[NotBlank]
        #[Uuid]
        public string $planPriceId,
    ) {}
}
