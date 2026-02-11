<?php

declare(strict_types=1);

namespace App\Owner\Domain;

use App\Shared\Domain\Address;
use App\Shared\Domain\Id;

final readonly class Owner
{
    private function __construct(
        public Id $id,
        public Id $userId,
        public ?string $companyName = null,
        public ?string $taxId = null,
        public ?string $phone = null,
        public ?Address $address = null
    ) {}

    public static function create(
        Id $id,
        Id $userId,
        ?string $companyName = null,
        ?string $taxId = null,
        ?string $phone = null,
        ?Address $address = null,
    ): self {
        return new self($id, $userId, $companyName, $taxId, $phone, $address);
    }

    public static function restore(
        Id $id,
        Id $userId,
        ?string $companyName,
        ?string $taxId,
        ?string $phone,
        ?Address $address,
    ): self {
        return new self($id, $userId, $companyName, $taxId, $phone, $address);
    }

    public function update(
        ?string $companyName,
        ?string $taxId,
        ?string $phone,
        ?Address $address,
    ): self {
        return new self(
            $this->id,
            $this->userId,
            $companyName,
            $taxId,
            $phone,
            $address
        );
    }
}
