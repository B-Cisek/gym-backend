<?php

declare(strict_types=1);

namespace App\Subscription\Domain\Embeddable;

use App\Subscription\Domain\Currency;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;

#[Embeddable]
final class Money
{
    public function __construct(
        #[Column(type: Types::INTEGER)]
        private int $value,
        #[Column(enumType: Currency::class)]
        private Currency $currency,
    ) {}

    public function getValue(): int
    {
        return $this->value;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function setCurrency(Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }
}
