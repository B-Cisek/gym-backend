<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Doctrine\Embeddable;

use App\Subscription\Domain\Currency;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;

#[Embeddable]
class Money
{
    public function __construct(
        #[Column(type: Types::INTEGER)]
        private int $value,
        #[Column(enumType: Currency::class)]
        private Currency $currency,
    ) {
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}
