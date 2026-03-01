<?php

declare(strict_types=1);

namespace App\Shared\Domain;

readonly class Id
{
    private const string ID_FORMAT = '/^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}$/i';

    public function __construct(private string $id)
    {
        $this->valid();
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function toString(): string
    {
        return $this->id;
    }

    public function equals(self $otherId): bool
    {
        return static::class === $otherId::class && $this->id === $otherId->id;
    }

    private function valid(): void
    {
        if (!preg_match(self::ID_FORMAT, $this->id)) {
            throw new InvalidIdException($this->id);
        }
    }
}
