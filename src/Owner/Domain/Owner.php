<?php

declare(strict_types=1);

namespace App\Owner\Domain;

use App\Shared\Domain\Address;
use App\Shared\Domain\Id;

class Owner
{
    private function __construct(
        public Id $id,
        public Id $userId,
        public bool $profileCompleted,
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
        bool $profileCompleted = false,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $email = null,
        ?string $companyName = null,
        ?string $taxId = null,
        ?string $phone = null,
        ?Address $address = null,
        ?string $stripeCustomerId = null,
    ): self {
        return new self($id, $userId, $profileCompleted, $firstName, $lastName, $email, $companyName, $taxId, $phone, $address, $stripeCustomerId);
    }

    public static function restore(
        Id $id,
        Id $userId,
        bool $profileCompleted,
        ?string $firstName,
        ?string $lastName,
        ?string $email,
        ?string $companyName,
        ?string $taxId,
        ?string $phone,
        ?Address $address,
        ?string $stripeCustomerId,
    ): self {
        return new self($id, $userId, $profileCompleted, $firstName, $lastName, $email, $companyName, $taxId, $phone, $address, $stripeCustomerId);
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
            profileCompleted: $this->profileCompleted,
            firstName: $firstName ?? $this->firstName,
            lastName: $lastName ?? $this->lastName,
            email: $email ?? $this->email,
            companyName: $companyName ?? $this->companyName,
            taxId: $taxId ?? $this->taxId,
            phone: $phone ?? $this->phone,
            address: $address ?? $this->address,
            stripeCustomerId: $this->stripeCustomerId,
        );
    }

    public function withStripeCustomerId(string $stripeCustomerId): self
    {
        return new self(
            id: $this->id,
            userId: $this->userId,
            profileCompleted: $this->profileCompleted,
            firstName: $this->firstName,
            lastName: $this->lastName,
            email: $this->email,
            companyName: $this->companyName,
            taxId: $this->taxId,
            phone: $this->phone,
            address: $this->address,
            stripeCustomerId: $stripeCustomerId,
        );
    }

    public function setProfileCompleted(): self
    {
        return new self(
            id: $this->id,
            userId: $this->userId,
            profileCompleted: true,
            firstName: $this->firstName,
            lastName: $this->lastName,
            email: $this->email,
            companyName: $this->companyName,
            taxId: $this->taxId,
            phone: $this->phone,
            address: $this->address,
            stripeCustomerId: $this->stripeCustomerId,
        );
    }
}
