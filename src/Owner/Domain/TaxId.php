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

        if (!$this->isChecksumIsValid()) {
            throw new InvalidTaxIdException($taxId);
        }
    }

    private function isChecksumIsValid(): bool
    {
        $weights = [6, 5, 7, 2, 3, 4, 5, 6, 7];
        $sum = 0;

        for ($i = 0; $i < 9; ++$i) {
            $sum += (int) $this->value[$i] * $weights[$i];
        }

        $control = $sum % 11;

        if ($control === 10 || (int) $this->value[9] !== $control) {
            return false;
        }

        return true;
    }
}
