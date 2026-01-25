<?php

declare(strict_types=1);

namespace App\Auth\Domain;

use Stringable;

final readonly class Email implements Stringable
{
    private function __construct(
        public string $value
    ) {
        $this->validate($value);
    }

    public static function fromString(string $email): self
    {
        return new self($email);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private function validate(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException($email);
        }
    }
}
