<?php

declare(strict_types=1);

namespace App\Owner\Domain;

final readonly class TaxId implements \Stringable
{
    private function __construct(
        public string $value,
    ) {
        $this->validate($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromString(string $taxId): self
    {
        return new self(trim($taxId));
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    private function validate(string $taxId): void
    {
        if (!preg_match('/^\d{10}$/', $taxId)) {
            throw new InvalidTaxIdException($taxId);
        }
    }
}
