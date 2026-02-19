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
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $email = null,
        public ?string $companyName = null,
        public ?string $taxId = null,
        public ?string $phone = null,
        public ?Address $address = null,
        public ?string $stripeCustomerId = null,
    ) {}

    public static function create(
        Id $id,
        Id $userId,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $email = null,
        ?string $companyName = null,
        ?string $taxId = null,
        ?string $phone = null,
        ?Address $address = null,
        ?string $stripeCustomerId = null,
    ): self {
        return new self($id, $userId, $firstName, $lastName, $email, $companyName, $taxId, $phone, $address, $stripeCustomerId);
    }

    public static function restore(
        Id $id,
        Id $userId,
        ?string $firstName,
        ?string $lastName,
        ?string $email,
        ?string $companyName,
        ?string $taxId,
        ?string $phone,
        ?Address $address,
        ?string $stripeCustomerId,
    ): self {
        return new self($id, $userId, $firstName, $lastName, $email, $companyName, $taxId, $phone, $address, $stripeCustomerId);
    }

    public function update(
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $email = null,
        ?string $companyName = null,
        ?string $taxId = null,
        ?string $phone = null,
        ?Address $address = null,
    ): self {
        return new self(
            id: $this->id,
            userId: $this->userId,
            firstName: $firstName ?? $this->firstName,
            lastName: $lastName ?? $this->lastName,
            email: $email ?? $this->email,
            companyName: $companyName ?? $this->companyName,
            taxId: $taxId ?? $this->taxId,
            phone: $phone ?? $this->phone,
            address: $address ?? $this->address,
        );
    }
}
