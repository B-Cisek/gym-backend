<?php

declare(strict_types=1);

namespace App\Gym\Domain;

use App\Shared\Domain\Address;
use App\Shared\Domain\Id;

final readonly class Gym
{
    private function __construct(
        public Id $id,
        public Id $ownerId,
        public string $name,
        public Address $address,
    ) {}

    public static function create(
        Id $id,
        Id $ownerId,
        string $name,
        Address $address,
    ): self {
        return new self($id, $ownerId, $name, $address);
    }

    public static function restore(
        Id $id,
        Id $ownerId,
        string $name,
        Address $address,
    ): self {
        return new self($id, $ownerId, $name, $address);
    }

    public function update(string $name, Address $address): self
    {
        return new self($this->id, $this->ownerId, $name, $address);
    }
}
